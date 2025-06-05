import ContextSection from "../components/context/section";
import {useData} from "../providers/data";
import Header from "../ui/common/header";

export default function MainPage() {
  const {contextCategories} = useData();

  return (
    <>
      <div className="min-h-screen">
        <Header />

        <main className="container mx-auto px-4 py-8">
          <div className="space-y-8">
            {contextCategories.map((data) => {
              return (
                <ContextSection
                  key={data.context_key}
                  context_key={data.context_key}
                  categories={data.categories}
                />
              );
            })}
          </div>
        </main>
      </div>
    </>
  );
}
