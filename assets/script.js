document.addEventListener("DOMContentLoaded", function () {
    const checkboxes = document.querySelectorAll("input[type='checkbox'][name='produits[]']");
    const quantites = document.querySelectorAll("input[type='number']");
    const totalSpan = document.getElementById("total");
    const nombreArticlesSpan = document.getElementById("nombre-articles");

    function mettreAJourTotal() {
        let total = 0;
        let nombreArticles = 0;
        checkboxes.forEach((checkbox, index) => {
            if (checkbox.checked) {
                const quantite = parseInt(quantites[index].value) || 0;
                total += quantite * parseFloat(checkbox.value);
                nombreArticles += quantite;
            }
        });
        nombreArticlesSpan.textContent = nombreArticles;
        totalSpan.textContent = total.toFixed(2);
    }

    checkboxes.forEach((checkbox, index) => {
        checkbox.addEventListener("change", function () {
            quantites[index].disabled = !this.checked;
            if (!this.checked) quantites[index].value = 0;
            mettreAJourTotal();
        });
    });

    quantites.forEach(input => {
        input.addEventListener("input", mettreAJourTotal);
    });

    document.querySelectorAll(".btn-supprimer").forEach(button => {
        button.addEventListener("click", function () {
            const articleId = this.dataset.articleId;
            document.getElementById(`article-${articleId}`).remove();
            mettreAJourTotal();
        });
    });
});