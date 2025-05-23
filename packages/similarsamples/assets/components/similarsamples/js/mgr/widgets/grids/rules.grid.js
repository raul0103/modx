Ext.namespace("SimilarSamples.grid");

SimilarSamples.grid.Home = function (config) {
  config = config || {};
  Ext.applyIf(config, {
    id: "similarsamples-grid-main",
    url: SimilarSamples.config.connector_url,
    baseParams: { action: "mgr/rules/getlist" },
    fields: ["id", "name", "context_key", "options", "categories"],
    columns: [
      { header: "ID", dataIndex: "id", width: 50, sortable: true },
      {
        header: "Название",
        dataIndex: "name",
        width: 100,
      },
      {
        header: "Контекст",
        dataIndex: "context_key",
        width: 100,
      },
      {
        header: "Опции (через запятую)",
        dataIndex: "options",
        width: 100,
      },
      {
        header: "ID Категорий (через запятую)",
        dataIndex: "categories",
        width: 100,
      },
    ],
    items: [
      {
        html: "<p><b>Подсказка:</b> <ul><li><b>Название</b> - Используется в табах (переключателях вкладок)</li><li><b>Опции</b> - перечисление опций по которым будут выбраны товары</li><li><b>ID категорий</b> - из каких категорий выбирать товары</li></ul></p>",
        border: false,
        bodyCssClass: "panel-desc",
        style: { marginBottom: "10px" },
      },
    ],
    tbar: {
      cls: "similarsamples-tbar",
      items: [
        {
          text: "Обновить",
          handler: this.refresh,
          scope: this,
        },
        {
          text: "Добавить запись",
          handler: () => this.addItem(),
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

  SimilarSamples.grid.Home.superclass.constructor.call(this, config);
};

Ext.extend(SimilarSamples.grid.Home, MODx.grid.Grid);

// Контекстное меню
SimilarSamples.grid.Home.prototype.showContextMenu = function (record, coords) {
  var menu = new Ext.menu.Menu({
    items: [
      {
        text: "Редактировать",
        handler: () => this.updateRecord(record),
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

// Окно редактирования
SimilarSamples.grid.Home.prototype.updateRecord = function (record) {
  const win = new Ext.Window({
    title: `Редактирование записи ID ${record.get("id")}`,
    width: 500,
    height: 400,
    layout: "fit",
    modal: true,
    items: [
      {
        xtype: "form",
        id: "similarsamples-form-rules-edit",
        padding: 10,
        border: false,
        labelAlign: "top",
        items: [
          {
            xtype: "textfield",
            fieldLabel: "Название",
            name: "name",
            value: record.get("name"),
            anchor: "100%",
          },
          {
            xtype: "textfield",
            fieldLabel: "Контекст",
            name: "context_key",
            value: record.get("context_key"),
            anchor: "100%",
          },
          {
            xtype: "textfield",
            fieldLabel: "Опции",
            name: "options",
            value: record.get("options"),
            anchor: "100%",
          },
          {
            xtype: "textfield",
            fieldLabel: "Категории",
            name: "categories",
            value: record.get("categories"),
            anchor: "100%",
          },
        ],
      },
    ],
    buttons: [
      {
        text: "Сохранить",
        handler: function () {
          const form = Ext.getCmp("similarsamples-form-rules-edit").getForm();
          const values = form.getValues();

          Ext.Ajax.request({
            url: SimilarSamples.config.connector_url,
            params: {
              action: "mgr/rules/update",
              id: record.get("id"),
              name: values.name,
              context_key: values.context_key,
              options: values.options,
              categories: values.categories,
            },
            success: function (response) {
              const result = Ext.decode(response.responseText);
              if (result.success) {
                win.close();
                Ext.getCmp("similarsamples-grid-main").refresh();
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

SimilarSamples.grid.Home.prototype.addItem = function () {
  const win = new Ext.Window({
    title: "Добавить новую запись",
    width: 500,
    height: 400,
    layout: "fit",
    modal: true,
    items: [
      {
        xtype: "form",
        id: "similarsamples-form-rules-add",
        padding: 10,
        border: false,
        labelAlign: "top",
        items: [
          {
            xtype: "textfield",
            fieldLabel: "Название",
            name: "name",
            allowBlank: false,
            anchor: "100%",
          },
          {
            xtype: "textfield",
            fieldLabel: "Контекст",
            name: "context_key",
            allowBlank: false,
            anchor: "100%",
          },
          {
            xtype: "textfield",
            fieldLabel: "Опции",
            name: "options",
            allowBlank: false,
            anchor: "100%",
          },
          {
            xtype: "textfield",
            fieldLabel: "Категории",
            name: "categories",
            allowBlank: false,
            anchor: "100%",
          },
        ],
      },
    ],
    buttons: [
      {
        text: "Сохранить",
        handler: function () {
          const form = Ext.getCmp("similarsamples-form-rules-add").getForm();
          const values = form.getValues();

          // Проверка, чтобы поля не были пустыми
          if (form.isValid()) {
            Ext.Ajax.request({
              url: SimilarSamples.config.connector_url,
              params: {
                action: "mgr/rules/create",
                name: values.name,
                context_key: values.context_key,
                options: values.options,
                categories: values.categories,
              },
              success: function (response) {
                const result = Ext.decode(response.responseText);
                if (result.success) {
                  win.close();
                  Ext.getCmp("similarsamples-grid-main").refresh();
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

// Метод для удаления записи
const deleteRecord = function (record) {
  Ext.Msg.confirm(
    "Подтвердите действие",
    "Вы уверены, что хотите удалить эту запись?",
    function (btn) {
      if (btn === "yes") {
        Ext.Ajax.request({
          url: SimilarSamples.config.connector_url,
          params: {
            action: "mgr/rules/remove",
            id: record.get("id"),
          },
          success: function (response) {
            const result = Ext.decode(response.responseText);
            if (result.success) {
              Ext.getCmp("similarsamples-grid-main").refresh();
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

Ext.reg("similarsamples-grid-main", SimilarSamples.grid.Home);
