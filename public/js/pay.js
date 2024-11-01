jQuery(document).ready(function () {
  // api base url registered from /src/includes/wc-urubutopay-scripts.php
  const baseUrl = ApiBaseUrl;

  const orderStatus = jQuery(".upgfc-check-transaction").attr("data-attr");
  const transactionId = jQuery(".upgfc-check-transaction").attr(
    "data-transaction"
  );

  if (transactionId && orderStatus && orderStatus === "pending") {
    // run check transaction
    checkTransaction(transactionId);
  }

  function checkTransaction(transactionId) {
    const endpoint = "wc-urubutopay/transaction/check";
    const uri = `${baseUrl}${endpoint}`;

    const payload = { transaction_id: transactionId };

    const content = jQuery(".upgfc-check-transaction-content");
    const contentSuccessClass = "upgfc-check-transaction-content--success";
    const contentFailedClass = "upgfc-check-transaction-content--failed";
    const contentPendingClass = "upgfc-check-transaction-content--primary";
    const pendingMessage =
      "Thank you for initiating payment, Kindly wait for confirmation";

    const image = jQuery(".upgfc-check-transaction-image img");

    jQuery
      .ajax(uri, {
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify(payload),
      })
      .done(function (response, status) {
        if (response && status === "success" && response.data) {
          switch (response.data.transaction_status) {
            case TransactionStatus.PENDING:
            case TransactionStatus.INITIATED:
              content.removeClass(contentFailedClass);
              content.removeClass(contentSuccessClass);
              content.addClass(contentPendingClass);
              content.text(pendingMessage);
              image.attr("src", Assets["loading-icon-blue"]);
              setTimeout(() => {
                checkTransaction(transactionId);
              }, 5000);
              break;

            case TransactionStatus.FAILED:
              //display failed message
              content.removeClass(contentSuccessClass);
              content.removeClass(contentPendingClass);
              content.addClass(contentFailedClass);
              content.text("Payment failed");
              image.attr("src", Assets["failed-icon"]);
              break;
            case TransactionStatus.VALID:
            case TransactionStatus.PENDING_SETTLEMENT:
              //display success message
              content.removeClass(contentFailedClass);
              content.removeClass(contentPendingClass);
              content.addClass(contentSuccessClass);
              content.text("Payment succeed");
              image.attr("src", Assets["success-icon"]);
              break;

            default:
              content.removeClass(contentFailedClass);
              content.removeClass(contentSuccessClass);
              content.addClass(contentPendingClass);
              content.text(pendingMessage);
              image.attr("src", Assets["loading-icon-blue"]);
              setTimeout(() => {
                checkTransaction(transactionId);
              }, 5000);
          }
        }
      })
      .fail(function (error) {
        content.text(
          error && error.responseJSON && error.responseJSON.message
            ? error.responseJSON.message
            : "Something went wrong"
        );
        content.removeClass(contentSuccessClass);
        content.addClass(contentFailedClass);
        image.attr("src", Assets["failed-icon"]);
      });
  }
});
