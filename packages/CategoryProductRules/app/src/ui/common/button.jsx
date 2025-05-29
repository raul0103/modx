export default function Button({ children, ...rest }) {
  return (
    <button
      className="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2"
      {...rest}
    >
      {children}
    </button>
  );
}

export function SquareButton({ children, color = "red", ...rest }) {
  const colorMap = {
    red: {
      bg: "bg-red-500",
      hover: "hover:bg-red-600",
      ring: "focus:ring-red-300",
    },
    green: {
      bg: "bg-green-600",
      hover: "hover:bg-green-700",
      ring: "focus:ring-green-800",
    },
    blue: {
      bg: "bg-blue-600",
      hover: "hover:bg-blue-700",
      ring: "focus:ring-blue-800",
    },
  };

  const { bg, hover, ring } = colorMap[color] || colorMap.red;

  return (
    <button
      style={{ width: "42px", height: "42px" }}
      className={`text-white ${bg} ${hover} ${ring} focus:ring-4 font-medium rounded-lg text-sm`}
      {...rest}
    >
      {children}
    </button>
  );
}

// export function ButtonCyan({ children, ...rest }) {
//   return (
//     <button
//       {...rest}
//       className="relative inline-flex items-center justify-center p-0.5 mb-2 me-2 overflow-hidden text-sm font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-cyan-500 to-blue-500 group-hover:from-cyan-500 group-hover:to-blue-500 hover:text-white dark:text-white focus:ring-4 focus:outline-none focus:ring-cyan-200 dark:focus:ring-cyan-800"
//     >
//       <span className="relative px-5 py-2.5 transition-all ease-in duration-75 bg-white dark:bg-gray-900 rounded-md group-hover:bg-transparent group-hover:dark:bg-transparent">
//         {children}
//       </span>
//     </button>
//   );
// }
