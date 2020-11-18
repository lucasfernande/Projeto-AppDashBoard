// executa todas as ações apenas após o carregamento do DOM
$(document).ready(() => { 

	$('#documentacao').on('click', () => {
		$('#pagina').load('documentacao.html') // fazendo request do conteudo do arquivo documentacao.html para substituir o conteudo da div pagina
	})

	$('#suporte').on('click', () => {
		$('#pagina').load('suporte.html')
	})

	// recuperando valor selecionado no campo competência e mandando para o backend
	$('#competencia').on('change', e => {

		let competencia = $(e.target).val()

		$.ajax({ 
			type: 'GET', // método
			url: 'app.php', // url (para onde vai)
			data: `competencia=${competencia}`, // dados necessários
			dataType: 'json', // definindo que a resposta deve ser em um objeto literal json
			success: response => {  // o que acontece em caso de sucesso
				$('#numeroVendas').html(response.numeroVendas) 
				$('#totalVendas').html(response.totalVendas)
				$('#clientesAtivos').html(response.clientesAtivos)
				$('#clientesInativos').html(response.clientesInativos)
				$('#totalDespesas').html(response.totalDespesas)
			},
			error: error => { // o que acontece em caso de erro
				$('#numeroVendas').html('') 
				$('#totalVendas').html('')
				$('#clientesAtivos').html('')
				$('#clientesInativos').html('')
				$('#totalDespesas').html('')
			} 
		})
	})
})