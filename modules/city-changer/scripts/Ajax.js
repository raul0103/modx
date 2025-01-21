class Ajax {
  async getHTMLModals() {
    const formData = new FormData();
    formData.append("action", "get-html-modal");

    let html_modals = null;

    await fetch("/", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.text())
      .then((data) => {
        html_modals = data;
      })
      .catch((error) => {
        console.error("Ошибка при получении модальных окон:", error);
      });

    return html_modals;
  }
}

export default Ajax;
