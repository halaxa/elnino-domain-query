## _Čistě open sourcovaná knihovna zatím bez dalších úprav._
Díky firmě elnino za opensourcování 23.12.2021

# Doménové dotazy nad Doctrine 2

## Myšlenka

Doménovým dotazem máme na mysli znovupoužitelný kousek doménové logiky sloužící pro
dotazování se nad databází pro data. Cílem je skrýt konkrétní implementaci dotazování
v pojmenované a komponovatelné celky. Důvodem je redukce opakování stejných DQL podmínek,
které vyjadřují určitý stav v doméně a také redukce chyby, která by mohla vzniknout
nepřesným formulováním takové podmínky. Také docílíme vytknutí podmínek z repository metod
do samostatných komponovatelných objektů a repository tak nebude kynout jednoúčelovými metodami.

## Motivace

Cílem je místo něčeho takového:

```
SELECT user
FROM App\Entity\User user
INNER JOIN user.todos todo
WHERE user.active = 1
    AND todo.done = 0
```

Používat něco takového:

```php
$userRepo->match(new UsersWithPendingTodos);
```

A být třeba schopní kombinace logickými operátory:

```php
$userRepo->match(
    new AndX(
        new NotX(new UsersWithPendingTodos),
        new NotX(new UsersWithPendingProjects)
    )
);
```

## Jak na to

Základním stavebním kamenem tohoto systému jsou:

 1. **vlastní specifikace**
 2. **DefaultSpecificationRepository::match()**

### DefaultSpecificationRepository
Repository v názvu je jen zpola nesprávným označením toho, že tento objekt je naším výchozím bodem
pro komunikaci s databází. **Nedědí od `EntityRepository`**. Naopak zavádí koncept služby,
která kromě vyzobávání objektů z úložiště umožňuje i jejich ukládání a mazání (obsahuje třeba `persist()`,
`flush()`, `remove()` apod.). Podobně jako `EntityRepository` má nastavenou výchozí entitu,
nad kterou pracuje. Poskytnout ji ovšem nemusíme:

```php
$userRepo = new DefaultSpecificationRepository($entityManager, User::class);
$userRepo2 = new DefaultSpecificationRepository($entityManager);
```

O tom, co se stane, když ji neposkytneme, se dozvíme [dále](#entityclassproviderinterface).

Není tedy třeba sahat si pro `EntityManager` kvůli persistenci entit a pro repository kvůli
jejich získávání (varianta tahání repository z `EntityManager` ani není hodná zmínění).
Stačí jedna služba na obojí. Přesto je tato vlastnost jen zpříjemněním práce a s
doménovými dotazy souvisí.

### Píšeme vlastní specifikace
Vlastní specifikací označujeme implementaci `SpecInterface`. Jedná se o objekt, který
v sobě zapouzdřuje podmínku doménové logiky a tu zveřejňuje pomocí jediné metody
`expression()`. Ta vrací buď `SpecExpr` (jádro doménového výrazu) nebo opět specifikaci `SpecInterface`.
Specifikace by mohla vypadat nějak takto:

```php
use Doctrine\ORM\Query\Expr;

class ActiveUser implements SpecInterface
{
    function expression($alias = null)
    {
        $e = new Expr;
        return new SpecExpr(
            $e->eq("user.active", ':active'),
            [':active' => 1]
        );
    }
}
```

Objekt `SpecExpr` zde voláme se dvěma parametry. Prvním je doctrinní výraz a druhým je mapa
`:parametr => 'hodnota'`. Parametry bychom používat měli a to jak kvůli escapování tak kvůli
cachování SQL dotazů, které Doctrine provádí. Specifikaci pak
použijeme v metodě `match()`, která má jako parametry DQL SELECT clause a specifikace. Protože jsme
uvnitř naší specifikace použili natvrdo alias `'user'` musíme tuto specifikaci použít v metodě `match()`
se stejným aliasem:

```php
$result = $userRepo->match('user', new ActiveUser);
```

Tento způsob je možný, avšak spíše nepohodlný. Museli bychom vědět jaké aliasy specifikace na své
podmínky používá a ty v selectu použít. Řešením je parametr `$alias`, který je do metody `expression()`
předáván a možná jste si ho v příkladu již všimli. Je to právě ten alias, který předáváme do metody `match()`.
Ta alias dále předává specifikacím právě jako argument do metody `expression()`. Pokud tedy použijeme volání,
jak je ukázáno v posledním příkladě, můžeme specifikaci upravit a použít tak alias, o kterém
se rozhodne až v době volání `match()`:

```php
use Doctrine\ORM\Query\Expr;

class ActiveUser implements SpecInterface
{
    function expression($alias = null)
    {
        $e = new Expr;
        return new SpecExpr(
            $e->eq("$alias.active", ':active'),
            [':active' => 1]
        );
    }
}
```
Specifikace si tak nevynucuje alias, se kterým ji musíme použít a je univerzálnější. Alias v metodě `match()`
je navíc nepovinný, takže specku v tomto příkladu můžeme klidně zavolat i následovně:

```php
$result = $userRepo->match(new ActiveUser);
```

Metoda `match()` si v takovém případě alias vygeneruje z názvu třídy. Například z `\App\Entity\User`
udělá `user_`, což v rámci jednoho dotazu postačuje.
Ke každému automaticky vygenerovanému aliasu je přidán na konec symbol `_`, aby nedocházelo ke kolizi názvů
s DQL operátory nebo funkcemi (třeba `order`), protože DQL neumožňuje escapování symbolů na úrovni jazyka.

Nakonec, pokud přímo nevyžadujeme názvy vlastních parametrů v DQL dotazu, je možné jako návratovou hodnotu
ve vlastní specifikaci použít specifikaci `Params`, která parametry generuje za nás a zápis dále zjednodušuje.
Naše specifikace by pak mohla vypadat následovně:

```php
class ActiveUser implements SpecInterface
{
    function expression($alias = null)
    {
        return new Params(["$alias.active" => 1]);
    }
}
```

Proměnnou `$alias` nakonec nemusíme používat vůbec, protože se umí přidat sám. Stačí tedy:

```php
class ActiveUser implements SpecInterface
{
    function expression($alias = null)
    {
        return new Params(["active" => 1]);
    }
}
```

### Generování aliasů
Abychom mohli řadit přímo v metodě `match`, třeba specifikací `OrderBy` jako třeba v jednoduchém
příkladu takto:

```php
$repo->match('person', new PersonSpec, new OrderBy('person.age'))
```

musíme znát alias cílové entity. Pokud je tako entita ale najoinovaná někde v naší spece a alias má vygenerovaný,
mohl by to být problém. Tím víc, když se k kednomu aliacu může dojít dvěma cestami:

`SELECT person FROM Person person JOIN p.ratings rating`
nebo
`SELECT person FROM Person person JOIN person.comments comment JOIN comment.ratings rating`

Pokud nechceme, nemusíme v `match` metodě ani v `Join` specifikaci žádné aliasy nikde specifikovat
a vždy se vygenerují samy. Aliasy přijoinovaných entit se generují tak, aby reflektovaly cestu,
kterou k nim bylo dospěno a byly tak jednoznačné. Pokud bychom tedy měli svoji specifikaci `WithWellRatedComments`,
která v sobě bude joinovat tak jak máme ve druhém příkladu, vygenerovaný alias bude `person_comments_ratings_` 
(z prvního příkladu by byl `person_comments_`).
Ten pak snadno použijeme, pokud budeme podle `Rating` chtít řadit:

```php
$personRepo->match(new WithLastRatedComments, OrderBy('person_comments_ratings_.date DESC'))
```

### Join specifikace
Síla a znovupoužitelnost specifikací je patrná z toho, že je můžeme použít jak pro omezení výběru
primární entity, tak pro omezení podle joinované entity aniž by o tom joinovaná specifikace věděla. Řekněme, že
entita User kvůli separaci modulů neví o článcích. Když budu chtít vybrat články aktivních uživatelů a půjdu na to
tím pádem ze strany článku, mohu přesto specifikaci `ActiveUser` použít. Pomůže nám dvojice specifikací
`Join` a `LeftJoin`. Api mají stejné, rozdíl je jen ten, který je patrný z názvu:

```php
$articleRepo->match('article'
    new Join('article.user u',
        new ActiveUser;
    );
);
```

Prvním parametrem je join řetětec nebo `JoinExpr`, ve kterém stačí jen property. Druhým
parametrem (nepovinným) je `SpecInterface`, který chceme přijoinovat. Tím se nám otevírá cesta ke stromovému joinování
přes více entit, neboť `Join` samozřejmě implementuje `SpecInterface`:

```php
$ratingRepo->match('rating'
    new Join('rating.article a',
        new Join('a.user u'
            new ActiveUser;
        )
    )
);

Kvůli zjevnosti celého procesu je zde použit způsob, kdy můžeme aliasy předávat explicitně a naše specifikace
jej umí přijmout v konstruktoru. Každopádně joiny nám umožňují zjednodušení a do join řetězce aliasy nemusíme psát
aliasy částěčně nebo vůbec, protože se opět mohou vygenerovat. Tím, že zde stavíme dotaz stromově, nedojde ke kolizi
i když budou všechny generované. Jediné, co join znát musí, je vlastnost entity, na kterou chceme joinovat.
Celé se to pak dá zapsat i takto:

```php
$ratingRepo->match(
    new Join('article',
        new Join('user'
            new ActiveUser;
        )
    )
);
```

S případným OrderBy podle vygenerovaného aliasu:

```php
$ratingRepo->match(
    new Join('article',
        new Join('user'
            new ActiveUser;
        )
    ),
    new OrderBy('rating_article_user_.karma')
);
```

Vhodné je pak tento výraz zabalit do samostatné jedné specifikace, abychom si neznečisťovali uživatelský kód,
mohli ji znovupoužít a třeba pomocí ní ovlivňovat i počet vrácených výsledků, způsob hydratace nebo fetch join.
Prostě stejně jako bychom dříve pro tento use case vytvořili metodu na repository:

```php
class RatingsOfActiveUsers implements
    SpecInterface,
    QueryModifierInterface
{
    public function expression($ratingAlias = null)
    {
        return new Join('article',
            new Join('user',
                new ActiveUser
            )
        );
    }

    public function modifyQuery(Query $query)
    {
        $query->setHydrationMode(Query::HYDRATE_ARRAY);
    }
}
```

V metodě match se pak mohu rozhodnout o fetch joinu takto:

```php
$ratingRepo->match('rating_, rating_article_', new RatingsOfActiveUsers);
```

### Operátory
Specifikace implementující `SpecInterface` můžeme z vnějšku kombinovat pomocí logických operátorů, které jsou také
implementacemi `SpecInterface`. Můžeme tak samozřejmě kombinovat jak naše specifikace tak hotové `Join`y nebo jiné
operátory.

```php
$ratingRepo->match(
    new Join('article',
        new Join('user'
            new OrX(
                new ActiveUser,
                new RichUser
            )
        )
    )
);
```

nebo

```php
$ratingRepo->match(
    new AndX(
        new WellRated,
        new Join('article',
            new Join('user'
                new ActiveUser,
            )
        )
    )
);
```
a tak dále ...


Pokud budeme ve specifikaci implementující `SpecInterface` implementovat navíc třeba `QueryBuilderModifier`,
nesmíme spoléhat na správné vyhodnocení operátoru nad výrazy, které v naší specifikaci navěsíme na query builder
ručně a nevrátíme je pomocí `expression()`. Proto se také tento postup nedoporučuje.

### Více o metodě `match()`
Je to jediná metoda, kterou bychom se pro získávání dat měli snažit používat jak je vidět
v příkladech výše. Parametry jsou nepovinné a při zavolání naprázdno (bez argumentů) vrátí metoda
kolekci všech entit o které se daný repository stará (obdobně jako `findAll()`). První nepovinný
parametr je seznam aliasů, které chceme načíst, stejně jako v klauzuli `SELECT` v DQL.
První z těchto aliasů je zároveň brán jako primární `FROM` alias. Dále je již počet argumentů
variabilní a sestává z vlastních specifikací, kterými chceme dotaz formovat. Pokud je
specifikací jako argumentů více, je mezi ně implicitně položen operátor `AND`.

Metoda `match()` na našich specifikacích rozpoznává následující rozhranní:

1. `EntityClassProviderInterface` Poskytuje FQCN entity, ke které se implementující specifikace váže.
2. `SpecInterface` To už známe, vyjadřuje výraz identifikující doménový stav/podmínku
3. `QueryBuilderModifierInterface` Bude mu předán přímo QueryBuilder k modifikaci (opatrně!)
4. `QueryModifierInterface` Bude mu předán Query k modifikaci
5. `ResultFetcherInterface` Zajistí získání dat z Query
6. `ResultModifierInterface` Dostane výsledek dotazu (např. kolekci entit) opět k dodatečné modifikaci.

jejich klíčové metody jsou v tomto pořadí také volány.

#### EntityClassProviderInterface
Pokud je implementováno, je třídě dána prioritra před tou, která je v Repository jako primární. Pokud Repository entity
třídu nemá, je implementování tohoto interface nutné.

#### ResultFetcherInterface
Slouží k získání výsledku/dat z Query. Jeho metodě `fetchResult()` je předán Query a její návratová hodnota je brána
za výsledek dotazu. Odkaz na tento výsledek je pak dodatečně předán případné implementaci `ResultModifierInterface`.

#### Explicitní aliasy
Explicitně předávané aliasy do `match()` metody lze kombinovat se specifikacemi, které nastaví SCALAR nebo ARRAY mód
hydratace a dosáhnout tím optimalizovaných read-only dotazů typu:

```php
class HydrateArray implements ResultFetcherInterface {

    public function fetchResult(Query $query)
    {
        return $query->getArrayResult();
    }
}

$result = $userRepo->match('user.id, user.name', new ActiveUser, new HydrateArray);
```

### Vestavěné specifikace
Nacházejí se v `Elnino\DomainQuery\Spec` a jsou standardními implementacemi výše uvedených rozhranní. Často se
používají, případně ro bez nich ani pořádně nejde a proto si našly místo přímo v knihovně.

#### IndexBy
Nastaví `INDEX BY` DQL klauzuli. Použití:

```php
$result = $repo->match('order', new SomeOrderSpec, new IndexBy('order.id'));
```

Pokud nebude result array jinak modifikován, budou výsledky v něm indexovány pod klíčem, který odpovídá hodnotě
v IndexBy. Je to standardní chování Doctrine 2. Viz http://doctrine-orm.readthedocs.org/en/latest/tutorials/working-with-indexed-associations.html
