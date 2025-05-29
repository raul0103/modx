import FieldError from "../field-error";

export default function Input(props) {
  return (
    <>
      <label className="text-gray-500">{props.label}</label>
      <input
        type={props.type ?? "text"}
        onChange={props.onChange}
        className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
        required={props.required}
        placeholder={props.placeholder}
        readOnly={props.readOnly}
        value={props.value}
      />
      <FieldError error={props.error} />
    </>
  );
}
