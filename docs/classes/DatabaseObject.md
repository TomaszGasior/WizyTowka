*abstract* DatabaseObject
===

Klasa abstrakcyjna stanowiąca podstawę dla klas reprezentujących obiekty bazy danych. Innymi słowy, jest to ORM w uproszczonej formie. Umożliwia operacje CRUD — tworzenie, odczyt, zmianę i usuwanie rekordów.

Konkretna instancja klasy dziedziczącej po klasie `DatabaseObject` jest reprezentacją obiektową jednego rekordu określonej tabeli bazy danych.
Stworzenie nowego rekordu polega na utworzeniu nowej instancji klasy (zwyczajnie, za pomocą operatora `new`). Pobranie istniejących w bazie rekordów polega na użyciu statycznych metod `getAll()` lub `getById()` zwracających tablicę z przygotowanymi instancjami klasy bądź przygotowaną instancję klasy.
Klasy dziedziczące mogą oferować inne sposoby pobierania rekordów z wykorzystaniem klauzuli `WHERE` języka SQL za pośrednictwem chronionej metody `_getByWhereCondition()`.

**Konfiguracja klasy dziedziczącej polega na określeniu statycznych i chronionych pól:**

- `$_tableName` — nazwa tabeli bazy danych;
- `$_tableColumns` — tablica nazw poszczególnych kolumn tabeli (bez klucza podstawowego!);
- `$_tableColumnsJSON` — tablica nazw kolumn tabeli przechowujących obiekty zakodowane w formacie JSON (kod JSON jest automatycznie dekodowany przy odczycie i kodowany przy zapisie rekordu), opcjonalne;
- `$_tableColumnsTimeAtInsert` — tablica nazw kolumn tabeli z uniksowym znacznikiem czasu automatycznie umieszczanym przy tworzeniu rekordu (operacji `INSERT`), opcjonalne;
- `$_tableColumnsTimeAtUpdate` — jak wyżej, ale przy aktualizacji rekordu (operacji `UPDATE`), opcjonalne;
- `$_tablePrimaryKey` — nazwa kolumny klucza podstawowego, domyślnie `id`, opcjonalnie.

Klasa `DatabaseObject` implementuje metody magiczne `__get()`, `__set()`, `__isset()`, umożliwiając operowanie na polach rekordu jak na polach obiektu, oraz interfejs `IteratorAggregate`, pozwalając na iterowanie po polach rekordu w pętli. Implementuje również metodę `__debugInfo()` dla funkcji `var_dump()`.

## `__construct()`

Tworzy nowy rekord tabeli. Wszystkie pola rekordu otrzymują domyślną wartość `null`. Kolumnom określonym w polu `$_tableColumnsJSON` przypisywany jest pusty obiekt (instancja klasy `stdClass`).

## `__clone()`

Tworzy nowy rekord tabeli z dotychczasowymi danymi. Innymi słowy, przy klonowaniu obiektu jego kopia jest traktowana jako nowo utworzony rekord (kasowana jest wartość klucza podstawowego).

## `save() : bool`

Zapisuje rekord. Jeśli rekord jest nowo utworzonym rekordem, używane jest zapytanie SQL `INSERT`, a po pomyślnym dodaniu wartość klucza podstawowego jest uzupełniana. Jeśli rekord już istnieje, jest aktualizowany przy użyciu zapytania `UPDATE`.

Przed zapisem wartości kolumn zdefiniowanych w polu `$_tableColumnsJSON` zamieniane są na ciąg w formacie JSON, a do pól określonych w `$_tableColumnsTimeAtInsert` lub `$_tableColumnsTimeAtUpdate`, w zależności od kontekstu, zapisywany jest aktualny uniksowy znacznik czasu. Jeśli przy zapisie kodu JSON wystąpi błąd, zostanie rzucony wyjątek `DatabaseObjectException` #3.

## `delete() : bool`

Usuwa rekord. Po pomyślnym usunięciu z tabeli bazy danych, zachowując aktualne wartości pól (za wyjątkiem klucza podstawowego), staje się nowo utworzonym rekordem (jak za pomocą konstruktora; można go zapisać, by dodać go na nowo do tabeli z inną wartością klucza podstawowego).

Nie można usunąć rekordu, jeśli jest nowo utworzony (jeszcze nie zapisany w bazie danych).

## *static* `getAll() : array`

Zwraca tablicę gromadzącą wszystkie rekordy tabeli (każdy rekord jest indywidualną instancją klasy).

## *static* `getById($id) : ?DatabaseObject`

Zwraca rekord o wartości klucza podstawowego podanej w argumencie `$id` (instancję klasy). Jeśli taki rekord nie istnieje, zwraca `null`.

## *static protected* `_getByWhereCondition(string $sqlQueryWhere = null, array $parameters = [], bool $onlyOneRecord = false)`

Metoda stanowiąca podstawę dla innych metod pobierających istniejące rekordy. Wykonuje zapytanie `SELECT` w celu pobrania rekordów, wykorzystując przy tym [przypinanie parametrów wejściowych PDO](http://php.net/manual/en/pdo.prepared-statements.php).

Argument `$sqlQueryWhere` określa fragment zapytania SQL umieszczony po klauzuli `WHERE`, może być pusty. Argument `$parameters` to tablica używana przez PDO do przypięcia wartości do odpowiadających parametrów obecnych w `$sqlQueryWhere`, może być pustą tablicą.

Standardowo zwracana jest tablica rekordów (instancji klas), a jeśli rekordów brak — pusta tablica. Jeśli argument `$onlyOneRecord` jest prawdą, zwracany jest tylko pierwszy rekord bądź fałsz, jeśli brak rekordów.

Przy pobieraniu rekordów kolumny określone w `$_tableColumnsJSON` są dekodowane do obiektów (instancji klasy `stdClass`). Jeśli przy parsowaniu kodu JSON wystąpi błąd, zostanie rzucony wyjątek `DatabaseObjectException` #3.