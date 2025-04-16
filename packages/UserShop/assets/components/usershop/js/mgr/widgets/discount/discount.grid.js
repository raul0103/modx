Ext.namespace("UserShop.grid");

UserShop.grid.UserDiscount = function (config) {
  config = config || {};
  Ext.applyIf(config, {
    id: "usershop-grid-discount",
    url: UserShop.config.connector_url,
    baseParams: { action: "mgr/discount/getlist" },
    fields: ["id", "user_id", "username", "discount"],
    columns: [
      { header: "ID", dataIndex: "id", width: 50, sortable: true },
      {
        header: "Пользователь",
        dataIndex: "username",
        width: 100,
        renderer: function (value, metaData, record) {
          const user_id = record.get("user_id");
          const url = `${MODx.config.manager_url}?a=security/user/update&id=${user_id}`;
          return `<a href="${url}" target="_blank">${value}</a>`;
        },
      },
      { header: "Персональная скидка", dataIndex: "discount", width: 200 },
    ],
    tbar: {
      cls: "usershop-tbar",
      items: [
        {
          text: "Обновить",
          handler: this.refresh,
          scope: this,
        },
        {
          text: "Добавить скидку",
          handler: () => this.addDiscount(),
          scope: this,
          style: {
            color: "#fff", // цвет текста
            backgroundColor: "#28a745", // цвет фона (зеленый)
            borderColor: "#218838",
          },
        },
      ],
    },
    listeners: {
      rowcontextmenu: function (grid, rowIndex, e) {
        e.stopEvent();
        var record = grid.getStore().getAt(rowIndex);
        this.showContextMenu(record, e.getXY());
      },
      scope: this,
    },

    autoHeight: true,
    paging: true,
    remoteSort: true,
    clicksToEdit: 2,
  });

  UserShop.grid.UserDiscount.superclass.constructor.call(this, config);
};

Ext.extend(UserShop.grid.UserDiscount, MODx.grid.Grid);

// Контекстное меню
UserShop.grid.UserDiscount.prototype.showContextMenu = function (
  record,
  coords
) {
  var menu = new Ext.menu.Menu({
    items: [
      {
        text: "Редактировать",
        handler: () => this.updateRecord(record),
        scope: this,
      },
      {
        text: "Удалить",
        handler: () => this.deleteRecord(record),
        scope: this,
      },
    ],
  });
  menu.showAt(coords);
};

// Метод для удаления записи
UserShop.grid.UserDiscount.prototype.deleteRecord = function (record) {
  Ext.Msg.confirm(
    "Подтвердите действие",
    "Вы уверены, что хотите удалить эту запись?",
    function (btn) {
      if (btn === "yes") {
        Ext.Ajax.request({
          url: UserShop.config.connector_url,
          params: {
            action: "mgr/discount/remove",
            id: record.get("id"),
          },
          success: function (response) {
            const result = Ext.decode(response.responseText);
            if (result.success) {
              Ext.getCmp("usershop-grid-discount").refresh();
            } else {
              MODx.msg.alert("Ошибка", result.message);
            }
          },
          failure: function () {
            MODx.msg.alert("Ошибка", "Ошибка при отправке запроса.");
          },
        });
      }
    }
  );
};

// Окно редактирования
UserShop.grid.UserDiscount.prototype.updateRecord = function (record) {
  const win = new Ext.Window({
    title: `Редактирование скидки ID ${record.get("id")}`,
    width: 500,
    height: 400,
    layout: "fit",
    modal: true,
    items: [
      {
        xtype: "form",
        id: "usershop-form-discount-edit",
        padding: 10,
        border: false,
        labelAlign: "top",
        items: [
          {
            xtype: "numberfield",
            fieldLabel: "Персональная скидка",
            name: "discount",
            value: record.get("discount"),
            anchor: "100%",
            height: 120,

            allowDecimals: false,
            minValue: 0,
            maxValue: 100,
          },
        ],
      },
    ],
    buttons: [
      {
        text: "Сохранить",
        handler: function () {
          const form = Ext.getCmp("usershop-form-discount-edit").getForm();
          const values = form.getValues();

          Ext.Ajax.request({
            url: UserShop.config.connector_url,
            params: {
              action: "mgr/discount/update",
              id: record.get("id"),
              discount: values.discount,
            },
            success: function (response) {
              const result = Ext.decode(response.responseText);
              if (result.success) {
                MODx.msg.alert("Успех", "Скидка обновлена");
                win.close();
                Ext.getCmp("usershop-grid-discount").refresh();
              } else {
                let message = "";
                result.data.forEach((element) => {
                  message += element.msg + "<br>";
                });
                if (message) {
                  MODx.msg.alert("Ошибка", message);
                }
              }
            },
            failure: function () {
              MODx.msg.alert("Ошибка", "Ошибка при отправке запроса.");
            },
          });
        },
        scope: this,
      },
      {
        text: "Отмена",
        handler: function () {
          win.close();
        },
      },
    ],
  });

  win.show();
};

// Функция для открытия окна добавления новой скидки
UserShop.grid.UserDiscount.prototype.addDiscount = function () {
  const win = new Ext.Window({
    title: "Добавить новую скидку",
    width: 500,
    height: 400,
    layout: "fit",
    modal: true,
    items: [
      {
        xtype: "form",
        id: "usershop-form-discount-add",
        padding: 10,
        border: false,
        labelAlign: "top",
        items: [
          {
            xtype: "textfield",
            fieldLabel: "ID пользователя",
            name: "user_id",
            allowBlank: false,
            anchor: "100%",
          },
          {
            xtype: "numberfield",
            fieldLabel: "Персональная скидка",
            name: "discount",
            allowBlank: false,
            minValue: 0,
            maxValue: 100,
            anchor: "100%",
          },
        ],
      },
    ],
    buttons: [
      {
        text: "Сохранить",
        handler: function () {
          const form = Ext.getCmp("usershop-form-discount-add").getForm();
          const values = form.getValues();

          // Проверка, чтобы поля не были пустыми
          if (form.isValid()) {
            Ext.Ajax.request({
              url: UserShop.config.connector_url,
              params: {
                action: "mgr/discount/create", // Действие для добавления скидки
                user_id: values.user_id,
                discount: values.discount,
              },
              success: function (response) {
                const result = Ext.decode(response.responseText);
                if (result.success) {
                  MODx.msg.alert("Успех", "Скидка добавлена");
                  win.close();
                  Ext.getCmp("usershop-grid-discount").refresh();
                } else {
                  let message = "";
                  result.data.forEach((element) => {
                    message += element.msg + "<br>";
                  });
                  if (message) {
                    MODx.msg.alert("Ошибка", message);
                  }
                }
              },
              failure: function () {
                MODx.msg.alert("Ошибка", "Ошибка при отправке запроса.");
              },
            });
          }
        },
        scope: this,
      },
      {
        text: "Отмена",
        handler: function () {
          win.close();
        },
      },
    ],
  });

  win.show();
};

Ext.reg("usershop-grid-discount", UserShop.grid.UserDiscount);
