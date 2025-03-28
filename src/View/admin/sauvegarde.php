<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Sauvegardes</title>
    <link rel="stylesheet" href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Assets/css/admin.css">
</head>
<body>
    <div class="container">
        <h2>Déposer un fichier de sauvegarde</h2>
        <p>Glissez et déposez un fichier SQLite (.db) ici :</p>
        
        <form id="uploadForm" action="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/upload_backup.php" method="POST" enctype="multipart/form-data">
            <input type="file" id="fileInput" name="backup_file" accept=".db" hidden>
            <div id="dropZone">Déposez votre fichier ici</div>
            <button type="submit" class="btn btn-success">Envoyer</button>
        </form>

        <div id="uploadMessage"></div>
        <form action="../../Web/upload_backup.php" method="post" enctype="multipart/form-data">
    <label for="backupFile">Choisir un fichier SQL :</label>
    <input type="file" name="backupFile" id="backupFile" accept=".sql" required>
    <button type="submit">Remplacer la sauvegarde</button>
</form>


    </div>

    <script>
        const dropZone = document.getElementById("dropZone");
        const fileInput = document.getElementById("fileInput");

        dropZone.addEventListener("dragover", (e) => {
            e.preventDefault();
            dropZone.classList.add("hover");
        });

        dropZone.addEventListener("dragleave", () => {
            dropZone.classList.remove("hover");
        });

        dropZone.addEventListener("drop", (e) => {
            e.preventDefault();
            dropZone.classList.remove("hover");

            const file = e.dataTransfer.files[0];
            fileInput.files = e.dataTransfer.files;
        });

        dropZone.addEventListener("click", () => fileInput.click());

        document.getElementById("uploadForm").addEventListener("submit", function(e) {
            e.preventDefault();
            
            let formData = new FormData(this);
            
            fetch("<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/upload_backup.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                let uploadMessage = document.getElementById("uploadMessage");
                uploadMessage.innerText = data.message;
                uploadMessage.style.color = data.status === "success" ? "green" : "red";

                if (data.status === "success") {
                    showToast("Sauvegarde mise à jour avec succès ✅");
                }
            })
            .catch(error => console.error("Erreur:", error));
        });

    </script>

    <style>
        #dropZone {
            width: 100%;
            padding: 20px;
            text-align: center;
            border: 2px dashed #007bff;
            background-color: #f8f9fa;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        #dropZone.hover {
            background-color: #e0e0e0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            text-align: center;
        }
    </style>
</body>
</html>
