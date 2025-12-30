import express from "express";

const app = express();
const PORT = process.env.PORT || 10000;

app.use(express.json());

app.get("/api/health", (req, res) => {
  res.json({ status: "ok" });
});

app.listen(PORT, () => {
  console.log("API running on port", PORT);
}); 