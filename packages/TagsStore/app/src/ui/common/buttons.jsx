export function ButtonIcon(props) {
  return (
    <button
      onClick={props.onClick}
      className={
        props.className
          ? props.className
          : `relative group text-gray-600 hover:text-${props.color}-600 p-1 `
      }
    >
      <i className={`fas fa-${props.icon}`}></i>
      {props.tooltip ? (
        <span className="absolute z-10 top-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-black rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
          {props.tooltip}
        </span>
      ) : (
        ""
      )}
    </button>
  );
}

export function ButtonGray({children, ...props}) {
  return (
    <button
      {...props}
      className={`bg-gray-200 text-gray-700 px-4 py-2 rounded-md mr-2 hover:bg-gray-300 transition ${
        props.className || ""
      }`}
    >
      {children}
    </button>
  );
}

export function ButtonPrimary({children, ...props}) {
  return (
    <button
      {...props}
      className={`bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition ${
        props.className || ""
      }`}
    >
      {children}
    </button>
  );
}
