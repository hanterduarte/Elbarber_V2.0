# Elbarber - Sistema de Gerenciamento para Barbearias

## Descrição
O Elbarber é um sistema de gerenciamento completo para barbearias, desenvolvido com Laravel 10 e Livewire 3. O sistema oferece funcionalidades para gerenciar clientes, agendamentos, serviços, produtos, vendas e muito mais.

## Funcionalidades Principais
- Gerenciamento de Clientes
- Agendamento de Serviços
- Controle de Estoque
- Vendas e PDV
- Gestão Financeira
- Relatórios
- Sistema de Permissões
- Perfis de Usuário (Admin, Gerente, Barbeiro, Recepcionista)

## Requisitos
- PHP 8.1 ou superior
- Composer
- MySQL 5.7 ou superior
- Node.js e NPM
- Git

## Instalação

1. Clone o repositório:
```bash
git clone https://github.com/seu-usuario/elbarber.git
cd elbarber
```

2. Instale as dependências do PHP:
```bash
composer install
```

3. Instale o pacote spatie/laravel-permission:
```bash
composer require spatie/laravel-permission
```

4. Crie a tabela de cache:
```bash
php artisan cache:table
```

5. Configure o arquivo .env:
```bash
cp .env.example .env
```
Edite o arquivo .env com suas configurações de banco de dados:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=elbarber
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

6. Gere a chave da aplicação:
```bash
php artisan key:generate
```

7. Execute as migrações e seeders:
```bash
php artisan migrate:fresh --seed
```

8. Instale as dependências do Node.js:
```bash
npm install
```

9. Compile os assets:
```bash
npm run dev
```

10. Inicie o servidor de desenvolvimento:
```bash
php artisan serve
```

## Acesso ao Sistema
Após a instalação, você pode acessar o sistema com os seguintes usuários:

- **Administrador**
  - Email: admin@elbarber.com
  - Senha: password

- **Gerente**
  - Email: gerente@elbarber.com
  - Senha: password

- **Barbeiro**
  - Email: barbeiro@elbarber.com
  - Senha: password

- **Recepção**
  - Email: recepcao@elbarber.com
  - Senha: password

## Estrutura do Projeto
- `app/` - Código fonte da aplicação
  - `Http/Controllers/` - Controladores
  - `Http/Livewire/` - Componentes Livewire
  - `Models/` - Modelos Eloquent
  - `Services/` - Serviços da aplicação
- `database/` - Migrações e seeders
- `resources/` - Views, assets e componentes
- `routes/` - Rotas da aplicação
- `tests/` - Testes automatizados

## Contribuição
1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/nova-feature`)
3. Commit suas mudanças (`git commit -m 'Adiciona nova feature'`)
4. Push para a branch (`git push origin feature/nova-feature`)
5. Abra um Pull Request

## Licença
Este projeto está licenciado sob a licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

## Suporte
Para suporte, entre em contato através do email: suporte@elbarber.com
