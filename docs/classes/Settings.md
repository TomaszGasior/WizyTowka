Settings
===

Klasa umożliwiająca dostęp do głównego pliku konfiguracyjnego systemu WizyTówka. Opiera swoje działanie na klasie `ConfigurationFile`. Nie ma potrzeby inicjacji klasy — plik konfiguracyjny zostanie załadowany automatycznie przed pierwszym użyciem.

Aby odczytać wartość ustawienia, należy podać jego nazwę jako argument metody `get()`. Aby móc modyfikować konfigurację bądź iterować po poszczególnych ustawieniach, należy wywołać metodę `get()` bez żadnego argumentu i operować na klasie `ConfigurationFile`.

## *static* `get($option = null)`

Jeśli podano argument `$option`, metoda zwraca wartość ustawienia o podanej nazwie. Jeżeli argumentu nie podano, zwracana jest instancja klasy `ConfigurationFile` przechowująca główny plik konfiguracyjny systemu.

Jeżeli plik konfiguracyjny jest uszkodzony, zostanie rzucony wyjątek #10.

## *static* `getDefault($option = null)`

Metoda działa identycznie jak metoda `get()`, lecz w kontekście domyślnego pliku konfiguracyjnego systemu, a nie konfiguracji bieżącej. Domyślny plik konfiguracyjny otwierany jest w trybie tylko do odczytu.