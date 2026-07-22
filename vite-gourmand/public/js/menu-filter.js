document.addEventListener('DOMContentLoaded', () => {
    const filters = document.querySelectorAll(
        '#priceMin, #priceMax, #theme, #regime, #numberOfPeople'
    );

    const menusContainer = document.getElementById('menus-container');

    function loadMenus() {
        const params = new URLSearchParams({
            priceMin: document.getElementById('priceMin').value,
            priceMax: document.getElementById('priceMax').value,
            theme: document.getElementById('theme').value,
            regime: document.getElementById('regime').value,
            numberOfPeople: document.getElementById('numberOfPeople').value
        });

        fetch('/menus/filter?' + params.toString())
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Erreur HTTP ${response.status}`);
                }

                return response.text();
            })
            .then(html => {
                menusContainer.innerHTML = html;
            })
            .catch(error => {
                console.error('Erreur du filtre :', error);
            });
    }

    filters.forEach(element => {
        element.addEventListener('change', loadMenus);
        element.addEventListener('keyup', loadMenus);
    });
});