const toggleSidebar = () => {
	const sidebar = document.getElementById('sidebar');
	const overlay = document.querySelector('.overlay');
	if (sidebar.style.right === '0px') {
		sidebar.style.right = '-100%';
		overlay.style.display = 'none';
		document.body.style.overflow = 'auto';
	}
	else {
		sidebar.style.right = '0px';
		overlay.style.display = 'block';
		document.body.style.overflow = 'hidden';
	}
}