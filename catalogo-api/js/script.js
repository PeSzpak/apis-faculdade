// Validação no navegador: impede consulta com código vazio
const formConsulta = document.getElementById('form-consulta');
if (formConsulta) {
    formConsulta.addEventListener('submit', (ev) => {
        const campo = document.getElementById('codigo');
        const erro = document.getElementById('erro-js');
        if (campo.value.trim() === '') {
            ev.preventDefault();
            erro.textContent = 'Informe o código do produto.';
            erro.classList.remove('oculto');
            campo.focus();
        }
    });
}

// Confirmação antes de excluir
document.querySelectorAll('.form-excluir').forEach((form) => {
    form.addEventListener('submit', (ev) => {
        if (!confirm('Deseja realmente excluir este produto?')) ev.preventDefault();
    });
});
