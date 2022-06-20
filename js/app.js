// main
addEventListener('DOMContentLoaded', () => {
  document.querySelector('#windows').addEventListener('click', (event) => {
    if (event.target.id == 'windows') {
      closePopup();
    }
  });

  //   getBasicInfo();
  //   updateCategories();
  //   updateItems();
  //   updateUsers();
  //   updateOrders();

  updateEmployees();
  updatePositions();
  updateServices();
  updateApartments();
  updateDeals();

  // openPage('employees');
  openPage('deals');
});

const POPUP_TIME = 500;

const openPage = (page) => {
  const pages = [
    ['employees', 'Сотрудники'],
    ['services', 'Услуги'],
    ['contracts', 'Договоры'],
    ['deals', 'Сделки'],
    ['apartments', 'Недвижимость'],
    ['clients', 'Клиенты'],
    ['positions', 'Должности'],
  ];

  // change aside button style
  const aside = document.querySelectorAll('aside')[0];
  const ul = aside.querySelectorAll('ul');
  for (let i in pages) {
    let elem =
      pages[i][0] == 'positions'
        ? ul[1].querySelector('li')
        : ul[0].querySelectorAll('li')[i];

    if (page == pages[i][0]) elem.classList.add('aside-li_focused');
    else elem.classList.remove('aside-li_focused');
  }

  // hide all pages
  new Promise((resolve, reject) => {
    for (let pageSet of pages) {
      let pageId = pageSet[0];
      document.getElementById('header-title').style.opacity = 0;
      document.getElementById(pageId).style.opacity = 0;
      setTimeout(() => {
        document.getElementById(pageId).style.display = 'none';
      }, 200);
    }

    setTimeout(() => {
      resolve();
    }, 220);
  }).then(() => {
    // change title
    document.getElementById('header-title').innerHTML = pages.find(
      (elem) => elem[0] == page
    )[1];
    document.getElementById('header-title').style.opacity = 1;

    // show page
    document.getElementById(page).style.display = 'block';
    document.getElementById(page).style.opacity = 1;
  });
};

const shadow = (visible) => {
  return new Promise((resolve, reject) => {
    let param = visible ? 'flex' : 'none';
    if (visible) {
      document.getElementById('windows').style.display = param;
      setTimeout(() => {
        document.getElementById('windows').style.opacity = 1;
      }, 10);
      setTimeout(() => {
        resolve();
      }, POPUP_TIME);
    } else {
      document.getElementById('windows').style.opacity = 0;
      setTimeout(() => {
        document.getElementById('windows').style.display = param;
        resolve();
      }, POPUP_TIME);
    }
  });
};

const popup = (id, visible) => {
  return new Promise((resolve, reject) => {
    let param = visible ? 'block' : 'none';
    if (visible) {
      document.getElementById(id).style.display = param;
      setTimeout(() => {
        document.getElementById(id).style.opacity = 1;
      }, 10);
      setTimeout(() => {
        resolve();
      }, POPUP_TIME);
    } else {
      document.getElementById(id).style.opacity = 0;
      setTimeout(() => {
        document.getElementById(id).style.display = param;
        resolve();
      }, POPUP_TIME);
    }
  });
};

let activePopup;

const openPopup = (id) => {
  activePopup = id;
  let p1 = shadow(true);
  let p2 = popup(id, true);
  return Promise.all([p1, p2]);
};

const closePopup = (id = activePopup) => {
  let p1 = shadow(false);
  let p2 = popup(id, false);
  return Promise.all([p1, p2]);
};

// addEventListener('DOMContentLoaded', () => {
//   document.querySelector('#windows').addEventListener('click', (event) => {
//     if (event.target.id == 'windows') {
//       closePopup();
//     }
//   });
// });

function reload() {
  document.location.reload();
}

function logout() {
  fetch('php/auth.php', {
    method: 'POST',
    cache: 'no-cache',
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      op: 'logout',
    }),
  })
    // .then(response => response.text())
    // .then(dataStr => {
    //     console.log(dataStr);
    // })
    .then((response) => response.json())
    .then((data) => {
      if (data['status'] == 'OK') reload();
      else {
        console.log(data);
        alert(data['msg']);
      }
    });
}

const HEADERS = {
  Accept: 'application/json',
  'Content-Type': 'application/json',
};

function sendData(args) {
  return new Promise((res) => {
    const method = 'method' in args ? args['method'] : 'POST';
    const headers = 'headers' in args ? args['headers'] : HEADERS;
    const body = JSON.stringify(args['body']);
    const thenFunc = 'then' in args ? args['then'] : () => {};
    const catchFunc = 'catch' in args ? args['catch'] : () => {};
    fetch('php/app.php', {
      method: method,
      headers: headers,
      body: body,
    })
      .then((response) => response.text())
      .then((body) => {
        console.log('raw response data:');
        console.log(body);
        try {
          return JSON.parse(body);
        } catch {
          throw Error(body);
        }
      })
      .then((data) => {
        console.log('JSON response data:');
        console.log(data);
        if (data['status'] == 'OK') {
          thenFunc(data);
        }
        res(data);
      })
      .catch(catchFunc);
  });
}

function removeEventListenersFrom(element) {
  let clone = element.cloneNode(true);
  element.parentNode.replaceChild(clone, element);
}

// изменение данных формы во всплывающем окне
function replacePopupData(type, id) {
  switch (type) {
    case 'positions':
      sendData({
        body: {
          op: 'get_position_data',
          position_id: id,
        },
      }).then((serverResponse) => {
        const position = serverResponse['position']['position'];
        const $input = document.getElementById('position-edit-input');
        let $button = document.getElementById('position-edit-btn');
        $input.value = position;
        removeEventListenersFrom($button);
        $button = document.getElementById('position-edit-btn');
        $button.addEventListener('click', (e) => {
          editPositionForm(id);
        });
      });
      break;
    case 'employees':
      break;
    case 'services':
      sendData({
        body: {
          op: 'get_services_data',
          service_id: id,
        },
      }).then((serverResponse) => {
        const service = serverResponse['service']['service'];
        const description = serverResponse['service']['description'];
        const price = serverResponse['service']['price'];
        const $service = document.getElementById('service-edit-input-service');
        const $description = document.getElementById(
          'service-edit-input-description'
        );
        const $price = document.getElementById('service-edit-input-price');
        let $button = document.getElementById('service-edit-btn');
        $service.value = service;
        $description.value = description;
        $price.value = price;
        removeEventListenersFrom($button);
        $button = document.getElementById('service-edit-btn');
        $button.addEventListener('click', (e) => {
          editServiceForm(id);
        });
      });
      break;
  }
}

// сотрудники

function updateEmployees() {
  const $container = document.getElementById('employees-container');
  sendData({
    body: {
      op: 'get_employees',
    },
  }).then((serverData) => {
    const employees = serverData['msg'];
    let content = '';
    if (employees == false) {
      content = '<h1 style="margin-left: 50px;">Нет пользователей</h1>';
    } else {
      content += `
        <tr class="employees-table__title-row">
        <td style="width: 20px"><div class="title">#</div></td>
        <td><div class="title">Имя</div></td>
        <td><div class="title">Фамилия</div></td>
        <td><div class="title">Отчество</div></td>
        <td><div class="title">Должность</div></td>
        <td style="width: 160px"><div class="title"></div></td>
        </tr>
    `;
      for (id in employees) {
        const item = employees[id];
        content += `
            <tr>
            <td style="width: 20px"><div class="field">${item['id']}</div></td>
            <td><div class="field">${item['first_name']}</div></td>
            <td><div class="field">${item['second_name']}</div></td>
            <td><div class="field">${item['patronymic']}</div></td>
            <td><div class="field">${item['position']}</div></td>
            <td style="width: 160px">
                <!--<button onclick="openPopup('popup-employee-edit'); replacePopupData('employees', '${item['id']}');">Подробнее</button>-->
            </td>
            </tr>
        `;
      }
    }
    $container.innerHTML = content;
  });

  updateEmployeeFormOptions();
}

function addEmployee(
  first_name,
  second_name,
  patronymic,
  role_id,
  position_id,
  birth_date,
  phone,
  email,
  username,
  password
) {
  return new Promise((resolve, reject) => {
    sendData({
      body: {
        op: 'add_employee',
        first_name: first_name,
        second_name: second_name,
        patronymic: patronymic,
        role_id: role_id,
        position_id: position_id,
        birth_date: birth_date,
        phone: phone,
        email: email,
        username: username,
        password: password,
      },
    })
      .then((data) => {
        if (data.status == 'OK') {
          resolve();
        } else {
          alert('Пользователь уде существует или введены неверные данные');
        }
      })
      .catch(() => {
        reject();
      });
  });
}

function addEmployeeForm() {
  const $username = document.getElementById('input-add-employee-username');
  const $password = document.getElementById('input-add-employee-password');
  const $name1 = document.getElementById('input-add-employee-name1');
  const $name2 = document.getElementById('input-add-employee-name2');
  const $name3 = document.getElementById('input-add-employee-name3');
  const $position = document.getElementById(
    'popup-employee-add__select-position'
  );
  const $role = document.getElementById('popup-employee-add__select-role');
  const $birth = document.getElementById('input-add-employee-birth');
  const $phone = document.getElementById('input-add-employee-phone');
  const $email = document.getElementById('input-add-employee-email');
  const first_name = $name1.value;
  const second_name = $name2.value;
  const patronymic = $name3.value;
  const role_id = $role.value;
  const position_id = $position.value;
  const birth_date = $birth.value;
  const phone = $phone.value;
  const email = $email.value;
  const username = $username.value;
  const password = $password.value;
  addEmployee(
    first_name,
    second_name,
    patronymic,
    role_id,
    position_id,
    birth_date,
    phone,
    email,
    username,
    password
  )
    .then((serverData) => {
      closePopup();
      updateEmployees();
    })
    .catch((err) => {
      console.log(err);
    });
  // const $employee = document.getElementById('position-add-input');
  // const value = $position.value;
  // addPosition(value)
  //   .then(() => {
  //     closePopup();
  //     updatePositions();
  //   })
  //   .catch((err) => {
  //     console.log(err);
  //   });
}

function updateEmployeeFormOptions() {
  const $addSelectPosition = document.getElementById(
    'popup-employee-add__select-position'
  );
  const $addSelectRole = document.getElementById(
    'popup-employee-add__select-role'
  );
  const $editSelectPosition = document.getElementById(
    'popup-employee-edit__select-position'
  );
  const $editSelectRole = document.getElementById(
    'popup-employee-edit__select-role'
  );
  sendData({
    body: {
      op: 'get_employee_options',
    },
  }).then((serverData) => {
    const positions = serverData['response']['positions'];
    const roles = serverData['response']['roles'];
    $addSelectPosition.innerHTML = '';
    for (let position of positions) {
      let option = document.createElement('option');
      option.value = position['position_id'];
      option.text = position['position'];
      $addSelectPosition.appendChild(option);
    }
    $addSelectRole.innerHTML = '';
    for (let role of roles) {
      let option = document.createElement('option');
      option.value = role['role_id'];
      option.text = role['role'];
      $addSelectRole.appendChild(option);
    }
    $editSelectPosition.innerHTML = '';
    for (let position of positions) {
      let option = document.createElement('option');
      option.value = position['position_id'];
      option.text = position['position'];
      $editSelectPosition.appendChild(option);
    }
    $editSelectRole.innerHTML = '';
    for (let role of roles) {
      let option = document.createElement('option');
      option.value = role['role_id'];
      option.text = role['role'];
      $editSelectRole.appendChild(option);
    }
    //
  });
}

// должности

function updatePositions() {
  const $container = document.getElementById('positions-container');
  sendData({
    body: {
      op: 'get_positions',
    },
  }).then(({ positions }) => {
    //const positions = serverData['positions'];
    let content = '';
    if (positions == false) {
      content = '<h1 style="margin-left: 50px;">Нет должностей</h1>';
    } else {
      content += `
        <tr class="employees-table__title-row">
        <td style="width: 20px">
            <div class="title">#</div>
        </td>
        <td>
            <div class="title">Должность</div>
        </td>
        <td style="width: 160px">
            <div class="title"></div>
        </td>
        <td style="width: 100px">
            <div class="title"></div>
        </td>
        </tr>
    `;
      for (item of positions) {
        content += `
            <tr>
            <td style="width: 20px">
                <div class="field">${item['position_id']}</div>
            </td>
            <td class="td-wide">
                <div class="field">${item['position']}</div>
            </td>
            <td style="width: 160px">
                <button onclick="openPopup('popup-position-edit'); replacePopupData('positions', '${item['position_id']}');">
                Редактировать
                </button>
            </td>
            <td style="width: 100px">
                <button class="table-btn__remove" onclick="removePosition('${item['position_id']}');">Удалить</button>
            </td>
            </tr>
        `;
      }
    }
    $container.innerHTML = content;
  });
}

function addPosition(position) {
  return new Promise((resolve, reject) => {
    sendData({
      body: {
        op: 'add_position',
        position: position,
      },
    })
      .then(() => {
        resolve();
      })
      .catch(() => {
        reject();
      });
  });
}

function addPositionForm() {
  const $position = document.getElementById('position-add-input');
  const value = $position.value;
  addPosition(value)
    .then(() => {
      closePopup();
      updatePositions();
    })
    .catch((err) => {
      console.log(err);
    });
}

function editPosition(id, position) {
  return new Promise((resolve, reject) => {
    sendData({
      body: {
        op: 'edit_position',
        position_id: id,
        position: position,
      },
    })
      .then(() => {
        resolve();
      })
      .catch(() => {
        reject();
      });
  });
}

function editPositionForm(id) {
  const $position = document.getElementById('position-edit-input');
  const value = $position.value;
  editPosition(id, value)
    .then(() => {
      closePopup();
      updatePositions();
    })
    .catch((err) => {
      console.log(err);
    });
}

function removePosition(id) {
  sendData({
    body: {
      op: 'remove_position',
      position_id: id,
    },
  }).then(() => {
    updatePositions();
  });
}

// услуги

function updateServices() {
  const $container = document.getElementById('services-container');
  sendData({
    body: {
      op: 'get_services',
    },
  }).then(({ services }) => {
    let content = '';
    if (services == false) {
      content = '<h1 style="margin-left: 50px;">Нет услуг</h1>';
    } else {
      content += `
          <tr class="employees-table__title-row">
            <td style="width: 20px"><div class="title">#</div></td>
            <td><div class="title">Наименование услуги</div></td>
            <td><div class="title">Стоимость</div></td>
            <td style="width: 160px"><div class="title"></div></td>
            <td style="width: 100px"><div class="title"></div></td>
          </tr>
      `;
      for (item of services) {
        content += `
          <tr>
            <td style="width: 20px"><div class="field">${item['service_id']}</div></td>
            <td><div class="field">${item['service']}</div></td>
            <td><div class="field">${item['price']}</div></td>
            <td style="width: 160px">
              <button onclick="openPopup('popup-service-edit'); replacePopupData('services', '${item['service_id']}');">
                Редактировать
              </button>
            </td>
            <td style="width: 100px">
              <button class="table-btn__remove" onclick="removeService('${item['service_id']}');">Удалить</button>
            </td>
          </tr>
        `;
      }
    }
    $container.innerHTML = content;
  });
}

function addService(service, description, price) {
  return new Promise((resolve, reject) => {
    sendData({
      body: {
        op: 'add_service',
        service: service,
        description: description,
        price: price,
      },
    })
      .then(() => {
        resolve();
      })
      .catch(() => {
        reject();
      });
  });
}

function addServiceForm() {
  const $service = document.getElementById('service-add-input-service');
  const $description = document.getElementById('service-add-input-description');
  const $price = document.getElementById('service-add-input-price');
  const service = $service.value;
  const description = $description.value;
  const price = $price.value;
  addService(service, description, price)
    .then(() => {
      closePopup();
      updateServices();
    })
    .catch((err) => {
      console.log(err);
    });
}

function editService(id, service, description, price) {
  return new Promise((resolve, reject) => {
    sendData({
      body: {
        op: 'edit_service',
        service_id: id,
        service: service,
        description: description,
        price: price,
      },
    })
      .then(() => {
        resolve();
      })
      .catch(() => {
        reject();
      });
  });
}

function editServiceForm(id) {
  const $service = document.getElementById('service-edit-input-service');
  const $description = document.getElementById(
    'service-edit-input-description'
  );
  const $price = document.getElementById('service-edit-input-price');
  const service = $service.value;
  const description = $description.value;
  const price = $price.value;
  editService(id, service, description, price)
    .then(() => {
      closePopup();
      updateServices();
    })
    .catch((err) => {
      console.log(err);
    });
}

function removeService(id) {
  sendData({
    body: {
      op: 'remove_service',
      service_id: id,
    },
  }).then(() => {
    updateServices();
  });
}

// недвижимость

function updateApartments() {
  const $container = document.getElementById('apartments-container');
  sendData({
    body: {
      op: 'get_apartments',
    },
  }).then(({ apartments }) => {
    let content = '';
    if (apartments == false) {
      content = '<h1 style="margin-left: 50px;">Нет недвижимости</h1>';
    } else {
      content += `
        <tr class="employees-table__title-row">
          <td style="width: 20px"><div class="title">#</div></td>
          <td><div class="title">Адрес</div></td>
          <td><div class="title">Компания</div></td>
          <td><div class="title">Дата постройки</div></td>
          <td><div class="title">Стоимость</div></td>
          <td style="width: 100px"><div class="title"></div></td>
        </tr>
      `;
      for (item of apartments) {
        content += `
          <tr>
            <td style="width: 20px"><div class="field">${item['apartment_id']}</div></td>
            <td><div class="field">${item['address']}</div></td>
            <td><div class="field">${item['developer']}</div></td>
            <td><div class="field">${item['construction_date']}</div></td>
            <td><div class="field">${item['price']}</div></td>
            <td style="width: 100px">
              <button class="table-btn__remove" onclick="removeApartment('${item['apartment_id']}');">Удалить</button>
            </td>
          </tr>
        `;
      }
    }
    $container.innerHTML = content;
  });
}

function addApartment(address, company, date, price) {
  return new Promise((resolve, reject) => {
    sendData({
      body: {
        op: 'add_apartment',
        address: address,
        company: company,
        date: date,
        price: price,
      },
    })
      .then(() => {
        resolve();
      })
      .catch(() => {
        reject();
      });
  });
}

function addApartmentForm() {
  const $address = document.getElementById('input-add-apartment-address');
  const $company = document.getElementById('input-add-apartment-company');
  const $date = document.getElementById('input-add-apartment-date');
  const $price = document.getElementById('input-add-apartment-price');
  const address = $address.value;
  const company = $company.value;
  const date = $date.value;
  const price = $price.value;
  addApartment(address, company, date, price)
    .then(() => {
      closePopup();
      updateApartments();
    })
    .catch((err) => {
      console.log(err);
    });
}

function removeApartment(id) {
  sendData({
    body: {
      op: 'remove_apartment',
      apartment_id: id,
    },
  }).then(() => {
    updateApartments();
  });
}

// сделки

function updateDeals() {
  const $container = document.getElementById('deals-container');
  sendData({
    body: {
      op: 'get_deals',
    },
  }).then(({ deals }) => {
    let content = '';
    if (deals == false) {
      content = '<h1 style="margin-left: 50px;">Нет сделок</h1>';
    } else {
      content += `
        <tr class="employees-table__title-row">
          <td style="width: 20px"><div class="title">#</div></td>
          <td><div class="title">Сделка</div></td>
          <td><div class="title">Клиент</div></td>
          <td><div class="title">Сотрудник</div></td>
          <td><div class="date">Дата</div></td>
          <td class="td-btn"><div class="btn">Договор</div></td>
          <td style="width: 100px"><div class="title"></div></td>
        </tr>
      `;
      for (item of deals) {
        content += `
          <tr>
            <td style="width: 20px"><div class="field">${item['deal_id']}</div></td>
            <td>
              <div class="field">${item['deal']}</div>
            </td>
            <td><div class="field">${item['client']}</div></td>
            <td><div class="field">${item['employee']}</div></td>
            <td><div class="field">${item['deal_date']}</div></td>
            <td class="td-btn">
              <div class="btn">
                <button onclick="openContract('${item['contract_id']}');">Документ</button>
              </div>
            </td>
            <td style="width: 100px">
              <button class="table-btn__remove" onclick="removeDeal('${item['deal_id']}');">Удалить</button>
            </td>
          </tr>
        `;
      }
    }
    $container.innerHTML = content;
  });

  updateDealsFormOptions();
}

function addDeal(
  deal,
  serviceId,
  apartmentId,
  clientName1,
  clientName2,
  clientName3,
  clientBirth,
  clientPhone,
  clientEmail
) {
  return new Promise((resolve, reject) => {
    sendData({
      body: {
        op: 'add_deal',
        deal: deal,
        service_id: serviceId,
        apartment_id: apartmentId,
        client_name1: clientName1,
        client_name2: clientName2,
        client_name3: clientName3,
        client_birth: clientBirth,
        client_phone: clientPhone,
        client_email: clientEmail,
      },
    })
      .then(() => {
        resolve();
      })
      .catch(() => {
        reject();
      });
  });
}

function addDealForm() {
  const $deal = document.getElementById('input-add-deal-deal');
  const $service = document.getElementById('select-add-deal__select-service');
  const $apartment = document.getElementById(
    'select-add-deal__select-apartment'
  );
  const $name1 = document.getElementById('input-add-deal-client-name1');
  const $name2 = document.getElementById('input-add-deal-client-name2');
  const $name3 = document.getElementById('input-add-deal-client-name3');
  const $birth = document.getElementById('input-add-deal-client-birth');
  const $phone = document.getElementById('input-add-deal-client-phone');
  const $email = document.getElementById('input-add-deal-client-email');
  const deal = $deal.value;
  const serviceId = $service.value;
  const apartmentId = $apartment.value;
  const clientName1 = $name1.value;
  const clientName2 = $name2.value;
  const clientName3 = $name3.value;
  const clientBirth = $birth.value;
  const clientPhone = $phone.value;
  const clientEmail = $email.value;
  addDeal(
    deal,
    serviceId,
    apartmentId,
    clientName1,
    clientName2,
    clientName3,
    clientBirth,
    clientPhone,
    clientEmail
  )
    .then(() => {
      closePopup();
      updateDeals();
    })
    .catch((err) => {
      console.log(err);
    });
}

function removeDeal(id) {
  sendData({
    body: {
      op: 'remove_deal',
      deal_id: id,
    },
  }).then(() => {
    updateDeals();
  });
}

function updateDealsFormOptions() {
  const $addSelectService = document.getElementById(
    'select-add-deal__select-service'
  );
  const $addSelectApartment = document.getElementById(
    'select-add-deal__select-apartment'
  );
  sendData({
    body: {
      op: 'get_deals_options',
    },
  }).then((serverData) => {
    const services = serverData['response']['services'];
    const apartments = serverData['response']['apartments'];
    $addSelectService.innerHTML = '';
    for (let service of services) {
      let option = document.createElement('option');
      option.value = service['id'];
      option.text = service['description'];
      $addSelectService.appendChild(option);
    }
    $addSelectApartment.innerHTML = '';
    for (let apartment of apartments) {
      let option = document.createElement('option');
      option.value = apartment['id'];
      option.text = apartment['description'];
      $addSelectApartment.appendChild(option);
    }
  });
}

function openContract(id) {
  window.open('./docview.php?v=' + String(id), '_blank');
}
