// cp - current_page
// tp - total_page
function generatePagination(cp, tp) {
  if (tp < 7) return Array.from({ length: tp }, (_, i) => i + 1);
  if (cp > 3 && cp < tp - 2) return [1, "...", cp - 1, cp, cp + 1, "...", tp];
  if (cp >= tp - 2) return [1, "...", tp - 2, tp - 1, tp];
  return [1, 2, 3, "...", tp];
}
