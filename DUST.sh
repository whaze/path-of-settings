#!/usr/bin/env bash

# Générer le nom du fichier de sortie avec hash (6 premiers caractères) + timestamp
output_file="$(echo $RANDOM | md5sum | cut -c1-6)_$(date +%Y%m%d_%H%M%S).txt"
script_path=$(realpath "$0")  # Chemin complet du script en cours d'exécution

# Afficher la structure des dossiers en début de fichier
echo "STRUCTURE DES DOSSIERS" >> "$output_file"
echo "--------------------------------------------------" >> "$output_file"
tree -I "vendor|node_modules|build|.*" --charset=ascii >> "$output_file"
echo -e "\n\n*****************************\n\n" >> "$output_file"
echo "CONTENU DES FICHIERS" >> "$output_file"
echo -e "--------------------------------------------------\n\n" >> "$output_file"

# Trouver tous les fichiers du répertoire courant récursivement
find . -type f \
    -not -path '*/\.*' \
    -not -path '*/vendor/*' \
    -not -path '*/node_modules/*' \
    -not -path '*/build/*' \
    | while read -r file; do
    file_path=$(realpath "$file")
    # Ignorer le fichier de sortie et le script en cours d'exécution
    if [[ "$file" != "./$output_file" && "$file_path" != "$script_path" ]]; then
        # Écrire le chemin relatif
        echo "${file:2}" >> "$output_file"
        # Ligne de séparation
        echo "--------------------------------------------------" >> "$output_file"
        # Écrire le contenu du fichier
        cat "$file" >> "$output_file"
        # Ajouter des séparateurs et lignes vides
        echo -e "\n\n*****************************\n\n" >> "$output_file"
    fi
done

echo "Fichier créé : $output_file"