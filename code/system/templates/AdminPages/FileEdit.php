<form method="post">
	<h3>Adres URL</h3>

	<fieldset>
		<div>
			<input type="text" id="fileFullURL" value="<?= HTML::escape($fileFullURL) ?>" readonly title="Adres URL tego pliku.">
		</div>
	</fieldset>

	<p class="information">Użyj powyższego adresu URL, by udostępnić plik w formie linku.</p>

	<h3>Zmiana nazwy</h3>

	<?= (new HTMLFormFields)
		->text('Nazwa pliku', 'nofilter_newFileName', HTML::escape($fileName), ['placeholder' => '(pozostaw dotychczasową nazwę)'])
	?>

	<p class="warning">Uwaga! Nie należy zmieniać nazwy pliku, jeśli został on wykorzystany na jakiejkolwiek stronie witryny (na przykład został dodany do treści strony czy do galerii zdjęć). W&nbsp;takim wypadku zmiana nazwy pliku spowoduje niepoprawne działanie strony — odnośnik do pliku przestanie działać, obrazek nie będzie się wczytywać.</p>

	<button>Zmień nazwę pliku</button>
</form>

<script>
(function(){
	document.querySelector('input#fileFullURL').addEventListener('click', function(){
		this.select();
	});
})();
</script>