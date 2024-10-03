
document.addEventListener('DOMContentLoaded', () => {
    const gameBoard = document.getElementById('game-board');
    const cards = document.querySelectorAll('.card');
    const numberOfCards = cards.length;

    // Calcul du nombre optimal de colonnes pour former un carré ou un rectangle équilibré
    const numberOfColumns = Math.ceil(Math.sqrt(numberOfCards));

    // Appliquer le style en fonction du nombre de colonnes
    gameBoard.style.display = 'grid';
    gameBoard.style.gridTemplateColumns = `repeat(${numberOfColumns}, 100px)`;
    gameBoard.style.gap = '10px';
});

let firstCard, secondCard;
let lockBoard = false;
let matchedPairs = 0;  // Pour suivre combien de paires ont été trouvées

function flipCard(cardElement) {
    const cards = document.querySelectorAll('.card');
    const numberOfCards = cards.length;

    if (lockBoard) return;

    // Si la carte est déjà retournée ou fait partie d'une paire trouvée, on ne fait rien
    if (cardElement === firstCard || cardElement.classList.contains('matched')) return;

    // Affiche l'image de la carte
    cardElement.querySelector('img').style.display = 'block';

    if (!firstCard) {
        // Si c'est la première carte sélectionnée
        firstCard = cardElement;
    } else {
        // Si c'est la deuxième carte sélectionnée
        secondCard = cardElement;
        lockBoard = true;

        // Envoi des IDs des cartes sélectionnées pour vérification
        fetch('/game/check-match', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                first_card: firstCard.dataset.id,
                second_card: secondCard.dataset.id
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (!data.match) {
                // Si les cartes ne correspondent pas, les retourner après 2 secondes
                setTimeout(() => {
                    firstCard.querySelector('img').style.display = 'none'; // Cacher la première carte
                    secondCard.querySelector('img').style.display = 'none'; // Cacher la deuxième carte
                    resetBoard();

                }, 2000);  // Délai de 2 secondes
            } else {
                // Si les cartes correspondent, les marquer comme appariées
                firstCard.classList.add('matched');
                secondCard.classList.add('matched');
                matchedPairs++;  // Incrémente le nombre de paires trouvées

                resetBoard(); // Débloque le plateau après la mise à jour

                // Vérification si toutes les paires ont été trouvées
                if (matchedPairs === numberOfCards / 2) {
                    alert('Félicitations, vous avez trouvé toutes les paires !');
                }
            }
        })
        .catch(error => {
            console.error('Il y a eu un problème avec la requête fetch :', error);
        });
    }
}
function resetBoard() {
    firstCard = null;
    secondCard = null;
    lockBoard = false;  // Débloque le plateau pour retourner de nouvelles cartes
}
