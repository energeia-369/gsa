package com.playarena.service;

import com.playarena.entity.Tournament;
import com.playarena.repository.TournamentRepository;
import org.springframework.stereotype.Service;
import java.util.List;

@Service
public class TournamentService {

    private final TournamentRepository tournamentRepository;

    public TournamentService(TournamentRepository tournamentRepository) {
        this.tournamentRepository = tournamentRepository;
    }

    public Tournament createTournament(Tournament tournament) {
        return tournamentRepository.save(tournament);
    }

    public List<Tournament> getAllTournaments() {
        return tournamentRepository.findAll();
    }

    public Tournament getTournamentById(Long id) {
        return tournamentRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Tournament not found"));
    }

    public Tournament updateTournament(Long id, Tournament updatedTournament) {
        Tournament tournament = getTournamentById(id);
        tournament.setName(updatedTournament.getName());
        tournament.setSport(updatedTournament.getSport());
        tournament.setDate(updatedTournament.getDate());
        tournament.setVenue(updatedTournament.getVenue());
        tournament.setRegistrationFee(updatedTournament.getRegistrationFee());
        tournament.setMaxTeams(updatedTournament.getMaxTeams());
        tournament.setCurrentTeams(updatedTournament.getCurrentTeams());
        return tournamentRepository.save(tournament);
    }

    public void deleteTournament(Long id) {
        tournamentRepository.deleteById(id);
    }
}