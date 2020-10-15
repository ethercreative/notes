function NotesField ([ns, siteId, elementId, userId, allowDeleting]) {
	const input = document.getElementById('fields-' + ns + '-input')
		, add   = document.getElementById('fields-' + ns + '-add')
		, spin  = document.getElementById('fields-' + ns + '-spin')
		, notes = document.getElementById('fields-' + ns + '-notes');

	add.addEventListener('click', async () => {
		const note = input.value.trim();
		input.value = '';
		if (note === '') return;
		spin.classList.remove('hidden');
		const { data } = await Craft.sendActionRequest('post', 'notes/field/add', {
			data: { siteId, elementId, userId, note },
		});
		const li = document.createElement('li')
			, p  = document.createElement('p')
			, s  = document.createElement('small');
		// TODO: add delete button
		p.textContent = note;
		s.innerHTML = data.meta;
		li.appendChild(p);
		li.appendChild(s);
		notes.insertBefore(li, notes.firstElementChild);
		spin.classList.add('hidden');
	});
}
