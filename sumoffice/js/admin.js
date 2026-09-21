/* SumOffice Office — admin settings: one field, one button, an honest status line. */
(function () {
	var state = OCP.InitialState.loadState('sumoffice', 'status');
	var input = document.getElementById('sumoffice-url');
	var button = document.getElementById('sumoffice-connect');
	var status = document.getElementById('sumoffice-status');
	function render(s) {
		var html = '';
		if (!s.officeInstalled) {
			html += '<p class="sumoffice-warn">Nextcloud Office (richdocuments) is not installed. Install it from the app store first — this app configures it.</p>';
		}
		if (s.errors && s.errors.length) {
			html += '<ul class="sumoffice-errors">' + s.errors.map(function (e) { return '<li>' + escapeHTML(e) + '</li>'; }).join('') + '</ul>';
		}
		if (s.connected) {
			html += '<p class="sumoffice-ok">✓ Connected to <b>' + escapeHTML(s.server || 'SumOffice') + '</b> — ' + (s.formats || []).map(function (f) { return '.' + escapeHTML(f); }).join(', ') + '. Open any spreadsheet or document from Files.</p>';
		} else if (s.url && !(s.errors && s.errors.length)) {
			html += '<p>Not connected.</p>';
		}
		status.innerHTML = html;
	}
	function escapeHTML(t) { return String(t).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
	input.value = state.url || '';
	render(state);
	button.addEventListener('click', function () {
		button.disabled = true; status.innerHTML = '<p>Checking ' + escapeHTML(input.value) + ' …</p>';
		fetch(OC.generateUrl('/apps/sumoffice/connect'), {
			method: 'POST',
			headers: { 'Content-Type': 'application/json', requesttoken: OC.requestToken },
			body: JSON.stringify({ url: input.value }),
		}).then(function (r) { return r.json(); }).then(function (s) {
			s.officeInstalled = s.officeInstalled !== undefined ? s.officeInstalled : state.officeInstalled;
			render(s);
		}).catch(function (e) { status.innerHTML = '<p class="sumoffice-warn">' + escapeHTML(e.message) + '</p>'; })
		.finally(function () { button.disabled = false; });
	});
})();
