const tagTypes = [
  {value: "resource", title: "Ресурс"},
  {value: "custom", title: "Кастомный"},
];

export default function TypeSelect({onChange, tagType}) {
  return (
    <div>
      <label
        htmlFor="recordType"
        className="block text-sm font-medium text-gray-700 mb-1"
      >
        Тип
      </label>
      <select
        value={tagType}
        onChange={(e) => onChange(e.target.value)}
        className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        {tagTypes.map((type) => (
          <option key={type.value} value={type.value}>
            {type.title}
          </option>
        ))}
      </select>
    </div>
  );
}
