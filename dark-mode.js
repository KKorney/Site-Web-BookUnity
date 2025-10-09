console.log("Script chargé ✅");

const toggleButton = document.getElementById('toggle-dark-mode');

if (!toggleButton) {
    console.error("Bouton non trouvé !");
} else {
    console.log("Bouton trouvé ✅");

    // Vérifier si le dark mode était activé dans le localStorage
    if (localStorage.getItem('dark-mode') === 'enabled') {
        document.body.classList.add('dark-mode');
        console.log("Dark mode activé au chargement 🌙");
    } else {
        console.log("Mode clair au chargement ☀️");
    }

    toggleButton.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');

        if (document.body.classList.contains('dark-mode')) {
            localStorage.setItem('dark-mode', 'enabled');
            console.log("Dark mode activé 🔛");
        } else {
            localStorage.setItem('dark-mode', 'disabled');
            console.log("Dark mode désactivé 🔴");
        }
    });
}
