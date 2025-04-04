document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("regionSearch");
    const suggestions = document.getElementById("autocomplete-results");

    async function fetchRegions() {
        try {
            const response = await fetch("/SAE3.01/Web/frontController.php?controller=api&action=getRegions");
            const regions = await response.json();
            console.log("Regions fetched from API:", regions); // Vérifie les données récupérées
            // Supprime les doublons dans les régions
            return [...new Set(regions)];
        } catch (error) {
            console.error("Erreur lors de la récupération des régions:", error);
            return [];
        }
    }

    searchInput.addEventListener("input", async function () {
        const query = searchInput.value.toLowerCase().trim(); // Supprime les espaces inutiles
        console.log("Query:", query); // Vérifie la saisie utilisateur

        // Vide les suggestions précédentes
        suggestions.innerHTML = "";

        // Si le champ est vide, masquer les suggestions et arrêter l'exécution
        if (query.length === 0) {
            suggestions.innerHTML = ""; // Vide les suggestions
            suggestions.style.display = "none"; // Masquer les suggestions
            return; // Arrêter l'exécution
        }

        const regions = await fetchRegions();
        console.log("Regions fetched:", regions); // Vérifie les données récupérées

        // Filtrer les régions correspondant à la saisie avec des lettres qui se suivent
        const filteredRegions = regions.filter(region => {
            const lowerCaseRegion = region.toLowerCase();
            return lowerCaseRegion.includes(query); // Vérifie que les lettres se suivent strictement
        });
        console.log("Filtered regions (before rendering):", filteredRegions); // Vérifie les résultats filtrés

        // Trier les régions par ordre alphabétique à partir de la lettre tapée
        filteredRegions.sort((a, b) => {
            const aIndex = a.toLowerCase().indexOf(query);
            const bIndex = b.toLowerCase().indexOf(query);

            // Priorité aux régions qui commencent par la lettre tapée
            if (aIndex === 0 && bIndex !== 0) return -1;
            if (bIndex === 0 && aIndex !== 0) return 1;

            // Sinon, tri alphabétique
            return a.localeCompare(b);
        });

        // Si aucune région ne correspond, masquer les suggestions
        if (filteredRegions.length === 0) {
            console.log("No matching regions found."); // Log pour déboguer
            suggestions.style.display = "none";
            return;
        }

        // Afficher les suggestions
        suggestions.style.display = "block";

        // Ajouter les suggestions filtrées
        filteredRegions.forEach(region => {
            // Vérifiez si la suggestion existe déjà dans le conteneur
            if ([...suggestions.children].some(child => child.textContent === region)) {
                return; // Ignorez les doublons
            }

            const suggestionItem = document.createElement("div");
            suggestionItem.textContent = region;
            suggestionItem.classList.add("suggestion-item");
            suggestionItem.addEventListener("click", () => {
                searchInput.value = region; // Remplit le champ avec la région sélectionnée
                suggestions.innerHTML = ""; // Vide les suggestions
                suggestions.style.display = "none"; // Masquer les suggestions après sélection
            });
            suggestions.appendChild(suggestionItem);
        });
    });

    // Masquer les suggestions si on clique en dehors
    document.addEventListener("click", (event) => {
        if (!searchInput.contains(event.target) && !suggestions.contains(event.target)) {
            suggestions.style.display = "none";
        }
    });
    
    document.getElementById("resetView").addEventListener("click", function () {
        searchInput.value = ""; // Vide le champ de recherche
        suggestions.innerHTML = ""; // Vide les suggestions
        suggestions.style.display = "none"; // Masque le conteneur des suggestions
    });
});