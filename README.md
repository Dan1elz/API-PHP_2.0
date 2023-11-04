# API-PHP_2.0
Essa é a segunda versão do meu projeto API-PHP-AJAX. uma Interface de Programação de Aplicações back-end para o gerenciamento de um CRUD utilizando os padroes REST e MVC. Foi criada para o aprofundamento dos meus conhecimentos em MVC (Model-View-Controller) e POO(Orientação a Objetos).

Em meus estudos de C# percebi a necessidade de aprender a fazer uma API se, a utilização do frameword laravel. Pois enfrentei problemas na compreenção dos conceitos de MVC. Espero agora, conseguir migrar para o C# com menores dificuldades.

Nesse projeto eu utilizei apenas PHP puro e a biblioteca JWT.

### DOCUMENTAÇÃO PARA O USO

- **Efetuar Registro**
    - *Método*: POST
    - *Parâmetros da Solicitação*: ['nameUser','lastnameUser','emailUser','passwordUser']
    - *Exemplo de Solicitação*: POST['/register']
        body: [
            {
                "nameUser": "Daniel",
                "lastnameUser": "Zanni",
                "emailUser": "danielzanni07@gmail.com",
                "passwordUser": "12345678"
            }
        ]
    - *Exemplo de Resposta*:
        [
            {
                "error":false,
                "message":"user successfully registered",
                "data":null
            }
        ]
    - *Tipos de Erros*:
        (Caso não passe os parametros necessarios)
        [
            {
                "error": true,
                "message": "Insufficient values" 
            }
        ] 
        (Caso o Email Já esteja em uso)
        [
            {
                "error": true,
                "message": "Email is already in use"
            }
        ]
        (Caso Aja algum erro na conexão com o banco)
        [
            {
                "error": true,
                "message": "error when trying to register user"
                
            }
        ]
- **Efetuar Login**
    - *Método*: POST
    - *Parâmetros da Solicitação*: ['emailUser','passwordUser']
    - *Exemplo de Solicitação*: POST['/login']
        body: [
            {
                "emailUser": "danielzanni07@gmail.com",
                "passwordUser": "12345678"
            }
        ]
    - *Exemplo de Resposta 1*:
        [
            {
                "error": false,
                "message": User successfully logged in",
                "data": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoxLCJleHAiOjE2OTkyMDQ5NjAsImlhdCI6MTY5OTExODU2MH0.aJQrt0ez5W4OmNayMxHbHLj5Ugo9t6_0oruqf5xX3uM"
            }
        ]
    - *Exemplo de Resposta 2*:
        [
            {
                "error": false,
                "message": "User logged in successfully, token reused",
                "data": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoxLCJleHAiOjE2OTkyMDQ5NjAsImlhdCI6MTY5OTExODU2MH0.aJQrt0ez5W4OmNayMxHbHLj5Ugo9t6_0oruqf5xX3uM"
            }
        ]
    - Tipos de Erros:
         (Caso não passe os parametros necessarios)
        [
            {
                "error": true,
                "message": "Insufficient values" 
            }
        ]
        (Caso o usuario não exista ou exista mais de um com os mesmos dados)
        [
            {
                "error": true,
                "message": "Login failed" 
            }
        ]
    
- **Para entrar**
    Lembrando que é necessario salvar o token do login na parte do usuario, para poder reutilizalo quando necessario.
     *Método*: GET
    - *Parâmetros da Solicitação*: Nenhum
    - *Exemplo de Solicitação*: POST['/getuser']
        Header: [
            {
                "Content-Type": "application/json",
                Authorization: `Bearer ${token}`,
            }
        ]
