import mysql.connector
import requests
import json

# Configuration de la connexion à MySQL
db_config = {
    'host': 'localhost',  # Remplacez par votre hôte MySQL
    'user': 'root',       # Remplacez par votre utilisateur MySQL
    'password': '',       # Remplacez par votre mot de passe
    'database': 'meteo'   # Remplacez par votre base de données
}

# URL de l'API
api_url = "https://public.opendatasoft.com/api/explore/v2.1/catalog/datasets/donnees-synop-essentielles-omm/records"

# Fonction pour récupérer les données avec pagination
def fetch_data(api_url):
    all_data = []
    offset = 0
    limit = 100  # Le nombre d'enregistrements par page
    
    while True:
        # Crée l'URL avec l'offset et la limite
        paginated_url = f"{api_url}?limit={limit}&offset={offset}"
        
        try:
            response = requests.get(paginated_url)  # Requête GET
            response.raise_for_status()  # Lève une erreur si le statut HTTP est différent de 200
            data = response.json()  # Convertit la réponse en JSON
            results = data.get('results', [])
            
            if not results:
                break  # Si il n'y a plus de données, on arrête la boucle

            all_data.extend(results)  # Ajoute les résultats à la liste globale
            offset += limit  # Augmente l'offset pour la page suivante
            
            print(f"Données récupérées avec succès. Offset actuel : {offset}")

        except requests.RequestException as e:
            print(f"Erreur lors de la récupération des données depuis l'API : {e}")
            break
    
    return all_data

# Récupérer toutes les données
data = fetch_data(api_url)

# Extraire les champs nécessaires
champs_voulus = ['numer_sta', 'coordonnees', 'altitude', 'libgeo', 'codegeo', 'nom_reg', 'nom_dept', 'nom']
donnees_a_inserer = [
    {
        'numer_sta': result.get('numer_sta'),
        'latitude': result.get('coordonnees', {}).get('lat'),
        'longitude': result.get('coordonnees', {}).get('lon'),
        'altitude': result.get('altitude'),
        'libgeo': result.get('libgeo') if result.get('libgeo') else 'Inconnu',  # Valeur par défaut
        'codegeo': result.get('codegeo') if result.get('codegeo') else 'Inconnu',  # Valeur par défaut si codegeo est manquant
        'nom_reg': result.get('nom_reg'),
        'nom_dept': result.get('nom_dept'),
    }
    for result in data
    if result.get('numer_sta')  # Vérification pour ignorer les résultats incomplets
]

# Fonction pour vérifier les duplicatas
def check_duplicate(cursor, numero):
    """Vérifie si un enregistrement avec le même numero existe déjà."""
    query = "SELECT COUNT(1) FROM stations WHERE numero = %s"
    cursor.execute(query, (numero,))
    result = cursor.fetchone()
    print(f"Duplicata check pour {numero}: {result[0]}")
    return result[0] > 0

try:
    # Connexion à MySQL
    connection = mysql.connector.connect(**db_config)
    cursor = connection.cursor()

    # Requête d'insertion
    insert_query = """
    INSERT INTO stations (numero, latitude, longitude, altitude, ville, code_geo, region, departement)
    VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
    """
    
    # Traitement des données et insertion
    for row in donnees_a_inserer:
        # Validation des données
        if not row['numer_sta'] or not row['latitude'] or not row['longitude']:
            print(f"Donnée invalide ignorée (incomplete) : {row}")
            continue

        # Remplacer les valeurs None ou vides par 'Inconnu' pour region et departement
        region = row['nom_reg'] if row['nom_reg'] else 'Inconnu'
        departement = row['nom_dept'] if row['nom_dept'] else 'Inconnu'
        
        # Vérification des duplicatas
        if check_duplicate(cursor, row['numer_sta']):
            print(f"Duplicata détecté, mise à jour ignorée pour numero : {row['numer_sta']}")
            continue
        
        # Exécution de l'insertion
        print(f"Insertion des données : {row}")
        cursor.execute(insert_query, (
            row['numer_sta'],       # Numero
            row['latitude'],        # Latitude
            row['longitude'],       # Longitude
            row['altitude'],        # Altitude
            row['libgeo'],          # Ville
            row['codegeo'],         # Code Geo (avec valeur par défaut 'Inconnu' si None)
            region,                 # Region (avec valeur par défaut 'Inconnu' si None)
            departement            # Departement (avec valeur par défaut 'Inconnu' si None)
        ))

    # Valider les changements
    connection.commit()
    print("Données insérées avec succès.")

except mysql.connector.Error as err:
    print(f"Erreur MySQL : {err}")
except Exception as e:
    print(f"Erreur inattendue : {e}")
finally:
    if connection.is_connected():
        cursor.close()
        connection.close()
        print("Connexion MySQL fermée.")
