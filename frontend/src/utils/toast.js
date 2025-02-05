import { useToast } from "primevue";

export function showToast(severity, summary, detail, life = 3000) {
    const toast = useToast();
    toast.add({
        severity: severity,
        summary: summary,
        detail: detail,
        life: life
    });
}
