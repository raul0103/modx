export default function FieldError(props) {
  return <>{props.error && <em className="text-red-500">{props.error}</em>}</>;
}
