export default function initAjax() {
  window.addOrderReview = async (event) => {
    event.preventDefault();

    let form = event.target;
    form.classList.add("loading");

    const response = await fetch("/", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        action: "add-order-review",
        ajax_connect: true,
        content: form.content.value,
        order_id: window.order_id,
      }),
    });

    form.classList.remove("loading");

    if (!response.ok) {
      throw new Error(`Response status: ${response.status}`);
    }

    let data = await response.json();

    if (data.success) {
      notifications.success(data.message);

      form.content.value = null;
    } else {
      notifications.error(data.message);
    }
  };

  window.addTicket = async (event) => {
    event.preventDefault();

    let form = event.target;
    form.classList.add("loading");

    const response = await fetch("/", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        action: "add-ticket",
        ajax_connect: true,
        subject: form.subject.value,
        content: form.content.value,
      }),
    });

    form.classList.remove("loading");

    if (!response.ok) {
      throw new Error(`Response status: ${response.status}`);
    }

    let data = await response.json();

    if (data.success) {
      notifications.success(data.message);

      form.subject.value = null;
      form.content.value = null;
    } else {
      notifications.error(data.message);
    }
  };

  window.addProductReviews = async (event) => {
    event.preventDefault();

    let form = event.target;
    form.classList.add("loading");

    const response = await fetch("/", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        action: "add-product-reviews",
        ajax_connect: true,
        product_id: form.product_id.value,
        defects: form.defects.value,
        advantages: form.advantages.value,
        content: form.content.value,
        rating: form.rating.value,
      }),
    });

    form.classList.remove("loading");

    if (!response.ok) {
      throw new Error(`Response status: ${response.status}`);
    }

    let data = await response.json();

    if (data.success) {
      notifications.success(data.message);

      form.defects.value = null;
      form.advantages.value = null;
      form.content.value = null;
      form.rating.value = null;
    } else {
      notifications.error(data.message);
    }
  };
}
