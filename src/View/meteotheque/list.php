<?php
// Vérifier si des utilisateurs sont disponibles
if (isset($utilisateurs) && count($utilisateurs) > 0) {
?>
    <h1>Meteothèque</h1>

    <select id="selectUtilisateur">
        <option value="">-- Choisir un utilisateur --</option>
        <?php foreach ($utilisateurs as $utilisateur) { ?>
            <option value="<?= htmlspecialchars($utilisateur['id']) ?>">
                <?= htmlspecialchars($utilisateur['nom']) ?> <?= htmlspecialchars($utilisateur['prenom']) ?> (ID: <?= htmlspecialchars($utilisateur['id']) ?>)
            </option>
        <?php } ?>
    </select>

    <button id="viewMeteotheque">Voir Météothèque</button>
    <button id="resetPage">Réinitialiser</button>

    <br>

    <h2 id="resultTitle" style="display: none;"></h2>
    <div id="meteothequeContent" style="display: none;"></div>
    <div class="footer2"> </div>
    <script>
        // Fonction pour charger la météothèque d'un utilisateur
        async function loadMeteotheque(userId) {
            try {
                const response = await fetch(`frontController.php?action=readMeteothequeByUser&controller=meteotheque&user_id=${userId}`);
                
                if (!response.ok) {
                    throw new Error(`Erreur HTTP : ${response.status}`);
                }

                const data = await response.json();

                const contentDiv = document.getElementById('meteothequeContent');
                const title = document.getElementById('resultTitle');

                if (data.success && data.results && data.results.length > 0) {
                    // Afficher le titre avec le nom de l'utilisateur
                    title.innerText = `Meteothèque de "${data.user.nom} ${data.user.prenom}"`;
                    title.style.display = 'block';

                    // Générer l'affichage des météothèques
                    contentDiv.style.display = 'block';
                    contentDiv.innerHTML = `
                        <ul>
                            ${data.results.map(item => `
                                <li>
                                    <strong>Nom de la collection :</strong> ${item.nom}<br>
                                    <strong>Description :</strong> ${item.description}<br>
                                    <strong>Date :</strong> ${item.date}
                                </li>
                            `).join('')}
                        </ul>
                    `;
                } else {
                    contentDiv.innerHTML = `<p>${data.message || 'Aucune météothèque trouvée pour cet utilisateur.'}</p>`;
                }
            } catch (error) {
                console.error('Erreur lors de la récupération des données :', error);
                alert('Une erreur est survenue lors de la récupération des données.');
            }
        }

        document.getElementById('viewMeteotheque').addEventListener('click', () => {
            const userId = document.getElementById('selectUtilisateur').value;
            if (!userId) {
                alert('Veuillez sélectionner un utilisateur.');
                return;
            }

            loadMeteotheque(userId);
        });

        document.getElementById('resetPage').addEventListener('click', () => {
            document.getElementById('selectUtilisateur').value = '';
            document.getElementById('meteothequeContent').style.display = 'none';
            document.getElementById('resultTitle').style.display = 'none';
        });
    </script>
<?php
} else {
    echo "<p>Aucun utilisateur disponible.</p>";
}
?>

<div class="footer2"> </div>


<style>
/* PAGE - METEOTHEQUE*/

/* Conteneur pour la présentation des météothèques */
#meteothequeContent {
    padding: 20px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    margin-top: 20px;
}


/* Titre des météothèques */
#resultTitle {
    font-size: 1.8rem;
    font-weight: bold;
    color: #004080;
    margin-bottom: 20px;
    text-align: center;
}

/* Liste des collections */
#meteothequeContent ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

#meteothequeContent ul li {
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 15px;
    padding: 15px;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
}
.footer2 {
        margin-bottom: 300px;
    }
/* Textes des collections */
#meteothequeContent ul li strong {
    font-weight: bold;
    color: #333;
}

#meteothequeContent ul li p {
    margin: 5px 0;
    font-size: 1rem;
    color: #555;
}


select#selectUtilisateur {
    display: block;
    width: 100%;
    max-width: 300px;
    margin: 20px auto;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 10px;
    font-size: 1rem;
}

/* Boutons */
button#viewMeteotheque, button#resetPage {
    display: block;
    width: 100%;
    max-width: 300px;
    margin: 10px auto;
    padding: 10px;
    background-color:rgba(73, 158, 248, 0.86);
    color: white;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
    text-align: center;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

button#viewMeteotheque:hover, button#resetPage:hover {
    background-color: #0056b3;
}

.footer2 {
    margin-bottom: 350px;
}

/* Style pour les messages vides */
#meteothequeContent p {
    font-size: 1rem;
    color: #888;
    text-align: center;
    margin-top: 20px;
}

/* Responsive Design */
@media screen and (max-width: 768px) {
    button#viewMeteotheque, button#resetPage {
        max-width: 100%;
    }

    #meteothequeContent ul li {
        padding: 10px;
    }
}


</style>