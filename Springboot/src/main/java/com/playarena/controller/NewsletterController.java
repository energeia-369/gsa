package com.playarena.controller;

import com.playarena.entity.NewsletterSubscriber;
import com.playarena.repository.NewsletterRepository;
import org.springframework.web.bind.annotation.*;
import java.util.List;
import java.util.Map;
import java.util.Optional;

@RestController
@RequestMapping("/api/newsletter")
@CrossOrigin(origins = "http://localhost:5173")
public class NewsletterController {

    private final NewsletterRepository repository;

    public NewsletterController(NewsletterRepository repository) {
        this.repository = repository;
    }

    @PostMapping("/subscribe")
    public Map<String, Object> subscribe(@RequestBody Map<String, String> payload) {
        String email = payload.get("email");
        if (email == null || email.trim().isEmpty()) {
            return Map.of("success", false, "message", "Email address is required");
        }

        Optional<NewsletterSubscriber> existing = repository.findByEmail(email);
        if (existing.isPresent()) {
            NewsletterSubscriber subscriber = existing.get();
            if ("UNSUBSCRIBED".equals(subscriber.getStatus())) {
                subscriber.setStatus("ACTIVE");
                repository.save(subscriber);
                return Map.of("success", true, "message", "Successfully resubscribed to newsletter!");
            }
            return Map.of("success", true, "message", "You are already subscribed to the newsletter!");
        }

        NewsletterSubscriber subscriber = new NewsletterSubscriber();
        subscriber.setEmail(email);
        subscriber.setStatus("ACTIVE");
        repository.save(subscriber);

        return Map.of("success", true, "message", "Successfully subscribed to the newsletter!");
    }

    @GetMapping("/subscribers")
    public List<NewsletterSubscriber> getSubscribers() {
        return repository.findAll();
    }
}
