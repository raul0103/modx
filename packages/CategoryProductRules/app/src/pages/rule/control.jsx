import Input from "../../ui/form/input";
import Button, { SquareButton } from "../../ui/common/button";
import PageTitle from "../../ui/common/page-title";
import { fetchCategory, fetchRule } from "../../lib/data";
import { useContext, useEffect, useState } from "react";
import { ControlRules } from "../../lib/actions";
import { RouterContext } from "../../providers/router";

export default function ControlRule() {
  const ctx = useContext(RouterContext);

  const url = new URL(location.href);
  const params = new URLSearchParams(url.search);
  const categoryId = params.get("categoryId");
  const isEdit = params.get("isEdit");

  const [category, setCategory] = useState(null);
  const [rules, setRules] = useState({});
  useEffect(() => {
    const loadCatgory = async () => {
      const response = await fetchCategory(categoryId);

      if (response.success === true) {
        setCategory(response.data);
      }
    };

    const loadRule = async () => {
      const response = await fetchRule(categoryId);

      if (response.success === true) {
        const rules_obj = JSON.parse(response.data.rules);
        setRules(rules_obj);
      }
    };

    if (isEdit) {
      loadRule();
    }

    loadCatgory();
  }, []);

  const [parents, setParents] = useState([]);
  const [options, setOptions] = useState({});

  useEffect(() => {
    if (rules.parents) {
      setParents(rules.parents);
    }

    if (rules.options) {
      setOptions(rules.options);
    }
  }, [rules]);

  const mainData = {
    parents: parents,
    options: options,
  };

  // handlerParents
  const hp = {
    set: (e) => {
      const value = e.target.value;
      setParents(value.split(","));
    },
  };
  // handlerOptions
  const ho = {
    add: (e) => {
      let value = { "": "" };
      setOptions({ ...options, ...value });
    },
    remove: (remove_key) => {
      const new_options = {};
      Object.entries(options).forEach(([key, value]) => {
        if (key !== remove_key) {
          new_options[key] = value;
        }
      });
      setOptions(new_options);
    },
    edit: {
      key: (e, need_key) => {
        const new_options = {};
        Object.entries(options).forEach(([key, value]) => {
          if (key === need_key) {
            new_options[e.target.value] = value;
          } else {
            new_options[key] = value;
          }
        });
        setOptions(new_options);
      },
      value: (e, need_key) => {
        const new_options = {};
        Object.entries(options).forEach(([key, value]) => {
          if (key === need_key) {
            new_options[key] = e.target.value;
          } else {
            new_options[key] = value;
          }
        });
        setOptions(new_options);
      },
    },
  };

  const saveRules = async () => {
    let action = "create-rules";
    if (isEdit) {
      action = "update-rules";
    }

    const data = JSON.stringify(mainData);
    const response = await ControlRules(
      action,
      category.id,
      category.context_key,
      data
    );
    if (response.success === true) {
      alert("Правило сохранено");
      if (!isEdit) {
        ctx.handleActivePage("ControlRule", {
          categoryId: category.id,
          isEdit: true,
        });
      }
    } else {
      alert("Ошибка");
    }
  };

  if (category) {
    return (
      <>
        <PageTitle>Создать правило</PageTitle>
        <div className="p-6 pt-0">
          {category.id} - {category.pagetitle}
        </div>
        <div className="bg-white shadow-md rounded-lg p-6 space-y-6">
          <div>
            <h2 className="text-xl font-semibold mb-2 border-b pb-1">
              Условия
            </h2>
            <Input
              label="ID Категорий через запятую"
              value={mainData.parents.join(",")}
              onChange={hp.set}
            />
          </div>

          <div>
            <div className="flex justify-between items-center mb-2 border-b pb-1">
              <h2 className="text-xl font-semibold">Редактируемые опции</h2>

              <SquareButton onClick={ho.add} color="green">
                +
              </SquareButton>
            </div>

            {Object.entries(options).map(([key, value], i) => {
              return (
                <div key={i} className="grid grid-cols-3 gap-4 items-end">
                  <div>
                    <Input
                      label="Ключ"
                      value={key}
                      onChange={(e) => ho.edit.key(e, key)}
                    />
                  </div>
                  <div>
                    <Input
                      label="Значение"
                      value={value}
                      onChange={(e) => ho.edit.value(e, key)}
                    />
                  </div>
                  <SquareButton onClick={() => ho.remove(key)} color="red">
                    -
                  </SquareButton>
                </div>
              );
            })}
          </div>

          <div className="text-right">
            <Button onClick={saveRules}>Сохранить</Button>
          </div>
        </div>
      </>
    );
  } else {
    return <div>Не удалось получить категорию</div>;
  }
}
