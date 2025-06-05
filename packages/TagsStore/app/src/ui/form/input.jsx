import FieldError from "./field-error";

export default function Input(props) {
  return (
    <div>
      <label className="block text-sm font-medium text-gray-700 mb-1">
        {props.label}
      </label>
      <input
        type={props.type ?? "text"}
        onChange={props.onChange}
        className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        required={props.required}
        placeholder={props.placeholder}
        readOnly={props.readOnly}
        value={props.value ?? ""}
      />
      <FieldError error={props.error} />
    </div>
  );
}
