// executa todas as ações apenas após o carregamento do DOM
$(document).ready(() => { 
	$('#documentacao').on('click', () => {
		$('#pagina').load('documentacao.html') // fazendo request do conteudo do arquivo documentacao.html para substituir o conteudo da div pagina
	})

	$('#suporte').on('click', () => {
		$('#pagina').load('suporte.html')
	})
})