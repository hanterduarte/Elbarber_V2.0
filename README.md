# ElBarber - Sistema de Gestão para Barbearias

## Descrição
ElBarber é um sistema de gestão completo para barbearias, desenvolvido em Laravel, que oferece funcionalidades para gerenciamento de clientes, serviços, produtos, vendas, agendamentos e muito mais.

## Requisitos
- PHP 8.2 ou superior
- Composer
- MySQL 5.7 ou superior
- Node.js e NPM (para assets)
- XAMPP (recomendado para ambiente de desenvolvimento)

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

3. Copie o arquivo de ambiente:
```bash
cp .env.example .env
```

4. Configure o arquivo .env com suas credenciais de banco de dados:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=elbarber
DB_USERNAME=root
DB_PASSWORD=
```

5. Gere a chave da aplicação:
```bash
php artisan key:generate
```

6. Execute as migrações e seeders:
```bash
php artisan migrate:fresh --seed
```

7. Instale as dependências do Node.js:
```bash
npm install
```

8. Compile os assets:
```bash
npm run dev
```

9. Inicie o servidor de desenvolvimento:
```bash
php artisan serve
```

## Usuários Iniciais

O sistema vem com os seguintes usuários pré-configurados:

### Administrador
- **Email:** admin@elbarber.com
- **Senha:** password
- **Permissões:** Acesso total ao sistema

### Gerente
- **Email:** manager@elbarber.com
- **Senha:** password
- **Permissões:** 
  - Gerenciamento de clientes
  - Gerenciamento de serviços
  - Gerenciamento de produtos
  - Gerenciamento de vendas
  - Gerenciamento de caixa
  - Visualização de relatórios
  - Gerenciamento de agendamentos

### Barbeiro
- **Email:** barber@elbarber.com
- **Senha:** password
- **Permissões:**
  - Gerenciamento de clientes
  - Gerenciamento de serviços
  - Gerenciamento de agendamentos

### Recepcionista
- **Email:** receptionist@elbarber.com
- **Senha:** password
- **Permissões:**
  - Gerenciamento de clientes
  - Gerenciamento de agendamentos
  - Gerenciamento de vendas
  - Gerenciamento de caixa

## Funcionalidades

- **Gestão de Usuários e Permissões**
  - Sistema de roles e permissões
  - Controle de acesso granular
  - Gerenciamento de usuários

- **Gestão de Clientes**
  - Cadastro de clientes
  - Histórico de serviços
  - Ficha de cliente

- **Gestão de Serviços**
  - Cadastro de serviços
  - Preços e duração
  - Categorização

- **Gestão de Produtos**
  - Controle de estoque
  - Cadastro de produtos
  - Alertas de estoque baixo

- **Gestão de Vendas**
  - Registro de vendas
  - Múltiplos métodos de pagamento
  - Comissões

- **Agendamentos**
  - Calendário de agendamentos
  - Confirmação automática
  - Lembretes

- **Caixa**
  - Abertura e fechamento
  - Movimentações
  - Relatórios

- **Relatórios**
  - Vendas
  - Serviços
  - Produtos
  - Caixa

## Contribuição

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## Licença

Este projeto está licenciado sob a licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

## Suporte
Para suporte, entre em contato através do email: suporte@elbarber.com
