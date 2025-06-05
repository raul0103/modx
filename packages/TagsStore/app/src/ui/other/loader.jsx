export default function loader() {
  return (
    <div className="flex flex-col items-center text-gray-500">
      <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900 mb-2" />
      <span>Загружаю данные...</span>
    </div>
  );
}
