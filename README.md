# 🎥 API Movie Suggestions (API de Sugestão de Filmes)

Esta é a documentação da API RESTful desenvolvida em PHP que gerencia informações sobre Filmes, Categorias e Avaliações.

---

## 🚀 Como Usar e Estrutura da Resposta

A API utiliza a arquitetura RESTful, comunicando o status da requisição através dos Códigos de Status HTTP.

### Status Codes (Códigos de Resposta)

| Código | Significado | Ocorrência Comum |
| :--- | :--- | :--- |
| **200 OK** | Sucesso. | Requisições GET, PUT/PATCH, DELETE bem-sucedidas. |
| **201 Created** | Criação de recurso bem-sucedida. | Requisições POST bem-sucedidas. |
| **400 Bad Request** | Erro de validação. | Campo obrigatório vazio ou ID inválido. |
| **404 Not Found** | Recurso não encontrado. | ID inexistente ou rota inválida. |
| **409 Conflict** | Conflito de integridade. | Tentativa de DELETE em registro com chave estrangeira (registros associados). |
| **500 Internal Server Error** | Erro interno do servidor. | Falha na execução da *query* no banco. |

### Estrutura do Corpo de Resposta

Em caso de sucesso (código 200, 201), a resposta terá a seguinte estrutura (sem os campos `erro` e `dados`):

```json
{
  "status": "success",
  "message": "Mensagem de confirmação.",
  "data": [
        "id": "2",
        "titulo": "Homem-Aranha",
        "ano_lancamento": "2003",
        "tempo_duracao": "132"
  ]
}