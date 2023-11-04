# API-PHP_2.0
<div style="text-align: justify">
Essa é a segunda versão do meu projeto API-PHP-AJAX. uma Interface de Programação de Aplicações back-end para o gerenciamento de um CRUD utilizando os padroes REST e MVC. Foi criada para o aprofundamento dos meus conhecimentos em MVC (Model-View-Controller) e POO(Orientação a Objetos).

Em meus estudos de C# percebi a necessidade de aprender a fazer uma API se, a utilização do frameword laravel. Pois enfrentei problemas na compreenção dos conceitos de MVC. Espero agora, conseguir migrar para o C# com menores dificuldades.

Nesse projeto eu utilizei apenas PHP puro e a biblioteca JWT. O envio da requisição deve ser feito em JSON, e a resposta retornara o mesmo. 
</div>

#
> DOCUMENTAÇÃO PARA O USO

    
<details>
<summary>EFETUAR REGISTRO</summary>

> - **Método**: POST
> - **Rota**: '/register'
> - **Parâmetros da Solicitação**: ['nameUser', 'lastnameUser', 'emailUser', 'passwordUser']
> - **Exemplo de Solicitação**:
    ```javascript
        fetch('localhost:8000/register', {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body:{
                "nameUser": "Daniel",
                "lastnameUser": "Zanni",
                "emailUser": "danielzanni07@gmail.com",
                "passwordUser": "12345678"
            },
        });
    ```
> - **Exemplo de Resposta**:
    ```javascript
        [
            {
                "error":false,
                "message":"user successfully registered",
                "data":null
            }
        ]
    ```
> - **Tipos de Erros**:
    ```javascript
        //Caso não passe os parametros necessarios
        [
            {
                "error": true,
                "message": "Insufficient values" 
            }
        ] 
        //Caso o Email Já esteja em uso
        [
            {
                "error": true,
                "message": "Email is already in use"
            }
        ]
        //Caso Aja algum erro na conexão com o banco
        [
            {
                "error": true,
                "message": "error when trying to register user"
                
            }
        ]
    ```
</details>
<details>
<summary>EFETUAR LOGIN</summary>

 > - **Método**: POST
 > - **Rota**: '/login'
 > - **Parâmetros da Solicitação**: ['emailUser','passwordUser']
 > - **Exemplo de Solicitação**:
 
    ```javascript
        fetch('localhost:8000/login', {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body:{
                "emailUser": "danielzanni07@gmail.com",
                "passwordUser": "12345678"
            }
        });
    ```
> - **Exemplo de Respostas**:
    ```javascript
        [
            {
                "error": false,
                "message": "User successfully logged in",
                "data": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoxLCJleHAiOjE2OTkyMDQ5NjAsImlhdCI6MTY5OTExODU2MH0.aJQrt0ez5W4OmNayMxHbHLj5Ugo9t6_0oruqf5xX3uM"
            }
            //OU 
            {
                "error": false,
                "message": "User logged in successfully, token reused",
                "data": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoxLCJleHAiOjE2OTkyMDQ5NjAsImlhdCI6MTY5OTExODU2MH0.aJQrt0ez5W4OmNayMxHbHLj5Ugo9t6_0oruqf5xX3uM"
            }
        ]
    ```
 > - **Tipos de Erros**:
    ```javascript
        //Caso não passe os parametros necessarios
        [
            {
                "error": true,
                "message": "Insufficient values" 
            }
        ]
        //Caso o usuario não exista ou exista mais de um com os mesmos dados
        [
            {
                "error": true,
                "message": "Login failed" 
            }
        ]
    ```
</details>
<details>
<summary>PARA ENTRAR</summary>

> - Lembrando que é necessario salvar o token do login na parte do usuario, para poder reutilizar quando necessario.
> - **Método**: GET
> - **Rota**: /getuser
> - **Parâmetros da Solicitação**: ['Token']
> - **Exemplo de Solicitação**: 
    ```javascript
        fetch('localhost:8000/getuser', {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                 Authorization: `Bearer ${token}`,
            },
        });
    ```
> - **Exemplo de Resposta**: 
    ```javascript
      [
        {
            "error": false,
            "message": "Data returned successfully",
            "data": {
                "id_user": 1,
                "name_user": "Daniel",
                "lastname_user": "Zanni",
                "email_user": "danielzanni07@gmail.com",
                "password_user": "12345678"
            }
        }
      ]
    ```
> - **Exemplos de Errors**:
    ```javascript
        //Caso não passe o token
        [
            {
                "error": true,
                "message": "Insufficient values" 
            }
        ] 
        //Caso o token seja invalido ou ocorra algum erro durante a requisição
        [
            {
                "error": true,
                "message": "Data return failed" 
            }
        ]
    ```
</details>
<details>
<summary>DELETANDO A CONTA</summary>

> - **Método**: DELETE
> - **Rota**: /delete
> - **Parâmetros da Solicitação**: ['Token']
> - **Exemplo de Solicitação**:
    ```javascript
        fetch('localhost:8000/delete', {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
                 Authorization: `Bearer ${token}`,
            },
        });
    ```
> - **Exemplo de Resposta**: 
    ```javascript
      [
        {
            "error": false,
            "message": "User successfully destroyed",
            "data": true
        }
      ]
    ```
> - **Exemplos de Errors**:
    ```javascript
        //Caso não passe o token
        [
            {
                "error": true,
                "message": "Insufficient values" 
            }
        ] 
        //Caso o token seja invalido ou ocorra algum erro durante a requisição
        [
            {
                "error": true,
                "message": "destruction failed" 
            }
        ]
    ```
</details>
<details>
<summary>ATUALIZANDO O USUARIO</summary>

> - **Método**: PUT
> - **Rota**: /update
> - **Parâmetros da Solicitação**: ['Token','nameUser','lastnameUser','passwordUser']
> - **Exemplo de Solicitação**:
    ```javascript
        fetch('localhost:8000/update', {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                 Authorization: `Bearer ${token}`,
            },
            body:{
                "nameUser": "Daniel",
                "lastnameUser": "Zanni",
                "passwordUser": "12345678"
            },
        });
    ```
> - **Exemplo de Resposta**: 
    ```javascript
    [
        {
            "error": false,
            "message": "User successfully logged in",
            "data": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjozLCJleHAiOjE2OTkyMTg0NDcsImlhdCI6MTY5OTEzMjA0N30._w1Xo0NphWiqPeN60diRcLp-z0k6RyJrbr5RN6j_kMM"
        }
    ]
    ```
> - **Exemplos de Errors**:
    ```javascript
        //Caso não passe o token e os dados nessesarios
        [
            {
                "error": true,
                "message": "Insufficient values" 
            }
        ] 
        //Caso as senhas sejam diferentes
        [
            {
                "error": true,
                "message": "Passwords are different" 
            }
        ]
        //Caso o token seja invalido ou ocorra algum erro durante a requisição
        [
            {
                "error": true,
                "message": "Data return failed" 
            }
        ]
        //Caso ouver erro na hora de criar o token
        [
            {
                "error": true,
                "message": "Error deleting and creating token, log in again" 
            }
        ]
    ```
</details>
