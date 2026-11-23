(function () {
    function criar(input, imagem) {
        let urlTemporaria = null;
        const imagemPadrao = imagem?.src ?? '';

        function liberarUrlTemporaria() {
            if (!urlTemporaria) {
                return;
            }

            URL.revokeObjectURL(urlTemporaria);
            urlTemporaria = null;
        }

        function mostrar(src) {
            if (!imagem) {
                return;
            }

            liberarUrlTemporaria();
            imagem.src = src || imagemPadrao;
            imagem.classList.toggle('comFoto', Boolean(src));
        }

        function mostrarArquivo(arquivo) {
            if (!imagem) {
                return;
            }

            if (!arquivo) {
                mostrar('');
                return;
            }

            liberarUrlTemporaria();
            urlTemporaria = URL.createObjectURL(arquivo);
            imagem.src = urlTemporaria;
            imagem.classList.add('comFoto');
        }

        imagem?.addEventListener('error', () => {
            if (imagem.src === imagemPadrao) {
                return;
            }

            liberarUrlTemporaria();
            imagem.src = imagemPadrao;
            imagem.classList.remove('comFoto');
        });

        if (input && imagem) {
            input.addEventListener('change', () => mostrarArquivo(input.files[0]));
        }

        return {
            mostrar,
            temImagem: () => imagem?.classList.contains('comFoto') === true
        };
    }

    window.PreviewImagem = { criar };
})();