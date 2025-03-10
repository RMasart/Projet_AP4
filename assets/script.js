const articles = document.querySelectorAll('.article');
let nombreArticles = 0;
let prixTotal = 0;

articles.forEach(article => {
    const quantiteInput = article.querySelector('.quantite input');
    const checkbox = article.querySelector('input[type="checkbox"]');
    const prix = parseInt(checkbox.value) || 0;

    quantiteInput.addEventListener('change', () => {
        let quantite = parseInt(quantiteInput.value);
        if (isNaN(quantite) || quantite < 0) {
            quantite = 0;
            quantiteInput.value = 0;
        }
        updateQuantiteAndTotal(article, quantite);
    });
});

function updateQuantiteAndTotal(article, quantite) {
    const checkbox = article.querySelector('input[type="checkbox"]');
    const prix = parseInt(checkbox.value) || 0;
    const quantiteInput = article.querySelector('.quantite input');

    let totalArticles = 0;
    let totalPrix = 0;

    articles.forEach(article => {
        const articleCheckbox = article.querySelector('input[type="checkbox"]');
        const articlePrix = parseInt(articleCheckbox.value) || 0;
        const articleQuantite = parseInt(article.querySelector('.quantite input').value);
        if (articleQuantite > 0) {
            totalArticles += articleQuantite;
            totalPrix += articlePrix * articleQuantite;
        }
    });

    nombreArticles = totalArticles;
    prixTotal = totalPrix;
    updatePanier();
}

function updatePanier() {
    document.getElementById('nombre-articles').textContent = nombreArticles;
    document.getElementById('total').textContent = prixTotal;
}