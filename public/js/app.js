document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.getElementById('barre-recherche');
    if (!searchInput) {
        return;
    }

    var containers = Array.prototype.slice.call(
        document.querySelectorAll('.produits')
    );
    if (containers.length === 0) {
        return;
    }

    function ensureEmptyMessage(container) {
        var next = container.nextElementSibling;
        if (next && next.classList.contains('search-empty')) {
            return next;
        }
        var message = document.createElement('p');
        message.className = 'search-empty';
        message.textContent = 'Aucun resultat pour votre recherche.';
        message.style.display = 'none';
        container.insertAdjacentElement('afterend', message);
        return message;
    }

    function filterContainer(container, query) {
        var cards = Array.prototype.slice.call(
            container.querySelectorAll('.produit')
        );
        var visibleCount = 0;
        cards.forEach(function (card) {
            var text = (card.textContent || '').toLowerCase();
            var match = query === '' || text.indexOf(query) !== -1;
            card.style.display = match ? '' : 'none';
            if (match) {
                visibleCount += 1;
            }
        });

        var message = ensureEmptyMessage(container);
        if (query !== '' && visibleCount === 0) {
            message.style.display = 'block';
        } else {
            message.style.display = 'none';
        }
    }

    function filterAll() {
        var query = searchInput.value.trim().toLowerCase();
        containers.forEach(function (container) {
            filterContainer(container, query);
        });
    }

    searchInput.addEventListener('input', filterAll);
    filterAll();
});
