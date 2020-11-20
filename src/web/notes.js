function NotesField ([ns, siteId, elementId, userId, allowDeleting]) {
	const input = document.getElementById('fields-' + ns + '-input')
		, add   = document.getElementById('fields-' + ns + '-add')
		, spin  = document.getElementById('fields-' + ns + '-spin')
		, notes = document.getElementById('fields-' + ns + '-notes');

	const save = async () => {
		const note = input.value.trim();
		input.value = '';
		if (note === '') return;
		spin.classList.remove('hidden');
		const { data } = await Craft.sendActionRequest('post', 'notes/field/add', {
			data: { siteId, elementId, userId, note },
		});
		const li = document.createElement('li')
			, d = document.createElement('div')
			, p = document.createElement('p')
			, s = document.createElement('small')
			, a = document.createElement('a');
		p.innerHTML = note.replace(/</g, '&lt;').replace(/\n/g, '<br/>');
		s.innerHTML = data.meta;
		d.appendChild(p);
		d.appendChild(s);
		li.appendChild(d);
		if (allowDeleting) {
			a.className = 'delete icon';
			a.setAttribute('role', 'button');
			a.setAttribute('title', Craft.t('app', 'Delete'));
			a.setAttribute('data-delete-note', data.id);
			a.addEventListener('click', NotesField.delete);
			li.appendChild(a);
		}
		notes.insertBefore(li, notes.firstElementChild);
		spin.classList.add('hidden');
	};

	input && input.addEventListener('keydown', e => {
		if (!(e.key === 'Enter' && e.metaKey)) return;
		save();
	});

	Array.from(document.querySelectorAll('[data-delete-note]')).forEach(a => {
		a.addEventListener('click', NotesField.delete);
	});

	add && add.addEventListener('click', save);
}

NotesField.delete = async e => {
	e.preventDefault();
	if (!confirm('Are you sure?')) return;
	const id = e.target.dataset.deleteNote|0;
	await Craft.sendActionRequest('post', 'notes/field/delete', {
		data: { id },
	});
	const li = e.target.parentNode;
	li.parentNode.removeChild(li);
};
