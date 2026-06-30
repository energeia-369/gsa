import API from "./api";

export const getAllTournaments = async () => {
  return await API.get("/tournaments");
};

export const registerTournament = async (registrationData) => {
  return await API.post(
    "/tournaments/register",
    registrationData
  );
};

export const createTournament = async (tournamentData) => {
  return await API.post(
    "/admin/tournaments",
    tournamentData
  );
};