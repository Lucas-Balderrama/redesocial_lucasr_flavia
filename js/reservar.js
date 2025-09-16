const selectQuarto = document.getElementById('quarto');
const selectRefeicao = document.getElementById('refeicao');
const totalElement = document.getElementById('total-reserva');
const inputTotalReserva = document.getElementById('input-total-reserva');

function calcularTotal() {
    const precoQuarto = parseFloat(selectQuarto.options[selectQuarto.selectedIndex].dataset.preco || 0);
    const precoRefeicao = parseFloat(selectRefeicao.options[selectRefeicao.selectedIndex].dataset.preco || 0);

    const total = precoQuarto + precoRefeicao;

    totalElement.textContent = `Total: R$ ${total.toFixed(2)}`;

    inputTotalReserva.value = total.toFixed(2);
}

selectQuarto.addEventListener('change', calcularTotal);
selectRefeicao.addEventListener('change', calcularTotal);

calcularTotal();
