function TournamentCard({ tournament }) {
  return (
    <div className="category-card">
      <h3>{tournament.name}</h3>
      <p>{tournament.date}</p>
      <p>Entry Fee: ₹{tournament.fee}</p>
    </div>
  );
}

export default TournamentCard;