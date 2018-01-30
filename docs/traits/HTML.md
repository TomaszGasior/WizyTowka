HTML
===

Trait gromadzący narzędzia przydatne w szablonach HTML, uwzględniające preferencje użytkownika.

## *static* `escape($text)`

Zwraca tekst `$text` ze znakami specjalnymi HTML (`<`, `>`, `"`, `'`, `&`) zamienionymi na ich bezpieczne przy wyświetlaniu odpowiedniki.

## *static* `unescape($text)`

Zwraca tekst `$text` z cofniętymi zmianami wprowadzonymi przez metodę `escape()`.

## *static* `correctTypography($text)`

Zwraca tekst `$text`, aplikując nań poprawki typograficzne zgodnie z ustawieniami systemu.

## *static* `formatDateTime($timestamp)`

Zwraca znacznik HTML `<time>` zawierający, sformatowaną zgodnie z ustawieniami systemu, datę i godzinę określoną w argumencie `$timestamp` jako uniksowy znacznik czasu bądź jako format zrozumiały dla funkcji [`strtotime()`](http://php.net/manual/en/datetime.formats.php).

## *static* `formatDate($timestamp)`

Działa jak `formatDateTime()`, ale zwraca tylko datę.

## *static* `formatTime($timestamp)`

Działa jak `formatDateTime()`, ale zwraca tylko godzinę.