const apiUrl = 'http://localhost/api-contact/api/';

const contactsTable = document.getElementById('contacts-table')

const loadContacts = async () => {
  try {
    const response = await fetch(apiUrl);
    if (!response.ok) throw new Error('Erro ao carregar os contatos.');

    const data = await response.json();
    if (data.length === 0) {
      contactsTable.innerHTML = `<tr><td colspan="4" class="text-center">Nenhum contato encontrado.</td></tr>`;
      return;
    }

    // Popula a tabela com os dados
    contactsTable.innerHTML = data.map(contact => `
      <tr>
        <td>${contact.name}</td>
        <td>${contact.email}</td>
        <td>${contact.phone}</td>
        <td>
          <div class="d-flex gap-2">
            <button class="btn btn-warning btn-sm" onclick="editContact(${contact.id}, '${contact.name}', '${contact.email}')">Editar</button>
        <button class="btn btn-danger btn-sm" onclick="deleteContact(${contact.id}, '${contact.name}')">Excluir</button>
          </div>
        </td>
      </tr>
    `).join('');
  } catch (error) {
    console.error('Erro:', error.message);
    contactsTable.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Erro ao carregar os contatos.</td></tr>`;
  }
};

const editContact = async (id, name, email) => {

  let editName = prompt('Editar nome: ', `${name}`);
  let editEmail = prompt('Editar email: ', `${email}`);

  if (editName == null || editName == "" || editEmail == null || editEmail == "") {
    alert('Nome ou email não podem ser vazios.');
    return;
  }

  try {
    const response = await fetch(apiUrl, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        id: id,
        name: editName,
        email: editEmail
      })
    });

    if (!response.ok) throw new Error('Erro ao editar o contato.');

    const result = await response.json();
    alert(result.message);
    loadContacts();

  } catch (error) {
    console.error('Erro:', error.message);
    alert('Erro ao editar o contato.');
  }
};

const deleteContact = async (id, name) => {
  if (!confirm(`Tem certeza que deseja excluir o contato "${name}"?`)) return

  try {
    const response = await fetch(apiUrl, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ id })
    });

    if (!response.ok) throw new Error('Erro ao excluir contato.');

    const result = await response.json();

    alert(result.message);

    loadContacts(); 

  } catch (error) {
    console.error('Erro:', error.message);
    alert('Erro ao excluir contato.');
  }
};

// Carrega os contatos ao carregar a página
document.addEventListener('DOMContentLoaded', loadContacts);