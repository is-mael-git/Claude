
function somenteDigitos(valor) {
    return String(valor ?? '').replace(/\D/g, '')
}

function mascaraCnpj(valor) {
    const cnpj = String(valor ?? '').replace(/[^A-Za-z0-9]/g, '').toUpperCase()

    if (!/^[A-Z0-9]{12}[0-9]{2}$/.test(cnpj)) {
        return valor ?? ''
    }

    return cnpj.replace(/^(.{2})(.{3})(.{3})(.{4})(\d{2})$/, '$1.$2.$3/$4-$5')
}

function mascaraTelefone(valor) {
    const digitos = somenteDigitos(valor)

    // celular com 9 na frente
    if (digitos.length === 11) {
        return digitos.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3')
    }

    // fixo
    if (digitos.length === 10) {
        return digitos.replace(/^(\d{2})(\d{4})(\d{4})$/, '($1) $2-$3')
    }

    return valor ?? ''
}

function mascaraCpf(valor){
    const digitos = somenteDigitos(valor);

    if (digitos.length !== 11){
        return valor ?? ''
    }

    return digitos.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/,'$1.$2.$3-$4')
}