ErrorHandler
===

Trait wychwytujący i obsługujący systemowe błędy PHP oraz niezłapane wyjątki.

Informacja o błędzie obejmuje: kod błędu (jeśli jest wyjątkiem) lub typ błędu (jeśli jest przekonwertowanym błędem PHP), wiadomość, ścieżkę do pliku i linię pliku oraz ścieżkę wykonywanych plików (backtrace).

## *static* `handleException($exception)`

Wychwytuje niezłapany wyjątek. Przeznaczona do zarejestrowania przez `set_exception_handler()`.

Dodaje informacje do dziennika błędów za pomocą metody `addToLog()`. Jeśli konfiguracja systemu to określa — drukuje komunikat o błędzie, używając metody `printAsPlainText()` (gdy zostanie wykryty typ MIME inny niż `text/html` lub gdy skrypt jest uruchamiany w wierszu polecenia) bądź `printAsHTML()`.

## *static* `handleError($number, $message, $file, $line)`

Konwertuje błąd systemowy PHP na wyjątek za pośrednictwem wbudowanej klasy `ErrorException`. Przeznaczona do zarejestrowania przez `set_error_handler()`.

Uwaga: rzucane jako wyjątek są wszystkie błędy, nawet typu `E_NOTICE`. Nie jest uwzględniana wartość dyrektywy `error_reporting`. Ignorowane są jedynie błędy, przy których wystąpieniu użyto [operatora kontroli błędów `@`](http://php.net/manual/en/language.operators.errorcontrol.php).

## *static private* `_addToLog($exception)`

Dopisuje informacje o błędzie do dziennika błędów. Znajduje się on domyślnie w folderze `data/config/errors.log`.

## *static private* `_printAsPlainText($exception)`

Wyświetla informacje o błędzie w formie zwykłego tekstu.

## *static private* `_printAsHTML($exception)`

Wyświetla informacje o błędzie w formie strony HTML.

## *static private* `_getPHPErrorName($code)`

Zwraca nazwę błędu PHP o kodzie `$code`.