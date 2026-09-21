<?php
declare(strict_types=1);
/** @var array $_ */
?>
<div id="sumoffice-admin" class="section">
	<h2>SumOffice Office</h2>
	<p class="settings-hint">Excel and Word files keep working — macros, Power Query, files back intact. Enter the public address of your SumOffice stack (SumSheet + SumDoc behind one address) and press Connect. Three steps to run the stack: <a href="https://sumoffice.com/nextcloud" target="_blank" rel="noopener">sumoffice.com/nextcloud</a>.</p>
	<div class="sumoffice-row">
		<input type="url" id="sumoffice-url" placeholder="https://office.example.com" />
		<button id="sumoffice-connect" class="primary">Connect</button>
	</div>
	<div id="sumoffice-status" class="sumoffice-status"></div>
</div>
