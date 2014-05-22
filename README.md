Inovarti_Quitar
===============

extensão com a API para integrar com o Quitar


API (SOAP)

Resource Name: quitar
Methods:
orders - Obtém a lista de pedidos com envío Correios.
          

Faults:
100 - Order not exists.	



Setup

1. Descompactar o arquivo ZIP e copiar os arquivos para o Magento.


2. Configurar o modulo Quitar no admin


"Sistema -> Configurações -> Inovarti -> Quitar
Endereço = Linha do campo de endereço para ser usado como logradouro.
Número = Linha do campo de endereço para ser usado como número.
Complemento = Linha do campo de endereço para ser usado como complemento"


3. Criar uma Função para o SOAP e marcar os recursos do Quitar. 


"Sistema -> Serviços Web -> SOAP/XML-RPC - Roles"

4. Criar um usuário para SOAP e marcar como Função a função criada anteriormente.

"Sistema -> Serviços Web -> SOAP/XML-RPC - Users"
