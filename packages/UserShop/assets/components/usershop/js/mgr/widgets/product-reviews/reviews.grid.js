Ext.namespace("UserShop.grid");

UserShop.grid.ProductReviews = function (config) {
  config = config || {};
  Ext.applyIf(config, {
    id: "usershop-grid-product-reviews",
    url: UserShop.config.connector_url,
    baseParams: {action: "mgr/product-reviews/getlist"},
    fields: [
      "id",
      "user_id",
      "username",
      "product_id",

      "defects",
      "advantages",
      "content",

      "rating",

      "publishedon",
      "status",
      "admin_response",
    ],
    columns: [
      {header: "ID", dataIndex: "id", width: 50, sortable: true},
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
      {
        header: "Товар ID",
        dataIndex: "product_id",
        width: 120,
        renderer: function (value, metaData, record) {
          const product_id = record.get("product_id");
          const url = `${MODx.config.manager_url}?a=resource/update&id=${product_id}`;
          return `<a href="${url}" target="_blank">${value}</a>`;
        },
      },
      {header: "Рейтинг", dataIndex: "rating", width: 100},

      {header: "Недостатки", dataIndex: "defects", width: 200},
      {header: "Достоинства", dataIndex: "advantages", width: 200},
      {header: "Отзыв", dataIndex: "content", width: 200},
      {
        header: "Статус",
        dataIndex: "status",
        width: 100,
        sortable: true,
        renderer: (value) =>
          ({
            pending: "На модерации",
            approved: "Одобрен",
            rejected: "Отклонён",
          }[value]),
      },
      {header: "Ответ админа", dataIndex: "admin_response", width: 150},
    ],
    tbar: {
      cls: "usershop-tbar",
      items: [
        {
          text: "Обновить",
          handler: this.refresh,
          scope: this,
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

  UserShop.grid.ProductReviews.superclass.constructor.call(this, config);
};

Ext.extend(UserShop.grid.ProductReviews, MODx.grid.Grid);

// Контекстное меню
UserShop.grid.ProductReviews.prototype.showContextMenu = function (
  record,
  coords
) {
  var menu = new Ext.menu.Menu({
    items: [
      {
        text: "Смотреть полностью",
        handler: () => {
          this.reviewRecord(record);
        },
        scope: this,
      },
      {
        text: "Редактировать",
        handler: () => {
          this.updateRecord(record);
        },
        scope: this,
      },
      {
        text: "Удалить",
        handler: () => deleteRecord(record),
        scope: this,
      },
    ],
  });
  menu.showAt(coords);
};

UserShop.grid.ProductReviews.prototype.reviewRecord = function (record) {
  const win = new Ext.Window({
    title: `Отзыв ID ${record.get("id")}`,
    width: 800,
    height: 800,
    layout: "fit",
    modal: true,
    items: [
      {
        xtype: "form",
        id: "usershop-form-review-edit",
        padding: 10,
        border: false,
        labelAlign: "top",
        items: [
          {
            xtype: "combo",
            fieldLabel: "Статус",
            name: "status",
            anchor: "100%",
            editable: false,
            store: [
              ["pending", "На модерации"],
              ["approved", "Одобрен"],
              ["rejected", "Отклонён"],
            ],
            value: record.get("status"),
          },
          {
            xtype: "textarea",
            fieldLabel: "Недостатки",
            name: "defects",
            value: record.get("defects"),
            anchor: "100%",
            height: 120,
          },
          {
            xtype: "textarea",
            fieldLabel: "Достоинства",
            name: "advantages",
            value: record.get("advantages"),
            anchor: "100%",
            height: 120,
          },
          {
            xtype: "textarea",
            fieldLabel: "Отзыв",
            name: "content",
            value: record.get("content"),
            anchor: "100%",
            height: 120,
          },
          {
            xtype: "textarea",
            fieldLabel: "Ответ администратора",
            name: "admin_response",
            value: record.get("admin_response"),
            anchor: "100%",
            height: 120,
          },
        ],
      },
    ],
  });

  win.show();
};

// Окно редактирования
UserShop.grid.ProductReviews.prototype.updateRecord = function (record) {
  const win = new Ext.Window({
    title: `Редактирование отзыва ID ${record.get("id")}`,
    width: 500,
    height: 400,
    layout: "fit",
    modal: true,
    items: [
      {
        xtype: "form",
        id: "usershop-form-review-edit",
        padding: 10,
        border: false,
        labelAlign: "top",
        items: [
          {
            xtype: "combo",
            fieldLabel: "Статус",
            name: "status",
            hiddenName: "status", // чтобы значение корректно отправлялось
            anchor: "100%",
            mode: "local",
            editable: false,
            triggerAction: "all",
            store: [
              ["pending", "На модерации"],
              ["approved", "Одобрен"],
              ["rejected", "Отклонён"],
            ],
            value: record.get("status"),
          },
          {
            xtype: "textarea",
            fieldLabel: "Ответ администратора",
            name: "admin_response",
            value: record.get("admin_response"),
            anchor: "100%",
            height: 120,
          },
        ],
      },
    ],
    buttons: [
      {
        text: "Сохранить",
        handler: function () {
          const form = Ext.getCmp("usershop-form-review-edit").getForm();
          const values = form.getValues();

          Ext.Ajax.request({
            url: UserShop.config.connector_url,
            params: {
              action: "mgr/product-reviews/update",
              id: record.get("id"),
              admin_response: values.admin_response,
              status: values.status,
            },
            success: function (response) {
              const result = Ext.decode(response.responseText);
              if (result.success) {
                win.close();
                Ext.getCmp("usershop-grid-product-reviews").refresh();
              } else {
                MODx.msg.alert("Ошибка", result.message);
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

// Метод для удаления записи
const deleteRecord = function (record) {
  Ext.Msg.confirm(
    "Подтвердите действие",
    "Вы уверены, что хотите удалить эту запись?",
    function (btn) {
      if (btn === "yes") {
        Ext.Ajax.request({
          url: UserShop.config.connector_url,
          params: {
            action: "mgr/product-reviews/remove",
            id: record.get("id"),
          },
          success: function (response) {
            const result = Ext.decode(response.responseText);
            if (result.success) {
              Ext.getCmp("usershop-grid-product-reviews").refresh();
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

Ext.reg("usershop-grid-product-reviews", UserShop.grid.ProductReviews);
